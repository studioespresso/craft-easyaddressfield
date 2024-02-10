<?php
/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use markhuot\CraftQL\Events\GetFieldSchema;
use studioespresso\easyaddressfield\fields\EasyAddressFieldFeedMe;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\services\CountriesService;
use studioespresso\easyaddressfield\services\FieldService;
use studioespresso\easyaddressfield\services\GeoLocationService;
use studioespresso\easyaddressfield\web\twig\variables\AddressVariable;
use yii\base\Event;

/**
 * Plugin represents the Easy Address Field plugin.
 *
 * @author Studio Espresso <support@studioespresso.co>
 * @since  1.0
 *
 * @property CountriesService $countries
 * @property GeoLocationService $geoLocation
 * @property FieldService $field
 */
class EasyAddressField extends Plugin
{
    /**
     * @var \studioespresso\easyaddressfield\EasyAddressField instance
     */
    public static $plugin;

    /**
     * @var bool
     */
    public bool $hasCpSettings = false;

    // Public Methods
    // =========================================================================
    public function init()
    {
        self::$plugin = $this;

        $this->setComponents([
            'field' => FieldService::class,
            'geoLocation' => GeoLocationService::class,
            'countries' => CountriesService::class,
        ]);

        // Register our fields
        Event::on(Fields::className(), Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = EasyAddressFieldField::class;
        });

        // Register our twig functions
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $variable = $event->sender;
            $variable->set('address', AddressVariable::class);
        });

        /** @phpstan-ignore-next-line */
        Event::on(EasyAddressFieldField::class, 'craftQlGetFieldSchema', function(GetFieldSchema $event) {
            $event->handled = true;
            $field = $event->sender;
            $object = $event->schema->createObjectType(ucfirst($field->handle) . 'EasyAddressField');

            $object->addStringField('name');
            $object->addStringField('street');
            $object->addStringField('street2');
            $object->addStringField('postalCode');
            $object->addStringField('city');
            $object->addStringField('state');
            $object->addStringField('country');
            $object->addStringField('lat');
            $object->addStringField('lng');
            $event->schema->addField($field)->type($object);
        });

        // If craftcms/feed-me is installed & activacted, hook here to register the field for import
        if (Craft::$app->getPlugins()->isPluginEnabled('feed-me')) {
            /** @phpstan-ignore-next-line */
            Event::on(\craft\feedme\services\Fields::class, \craft\feedme\services\Fields::EVENT_REGISTER_FEED_ME_FIELDS, function(RegisterFeedMeFieldsEvent $e) {
                $e->fields[] = EasyAddressFieldFeedMe::class;
            });
        }
    }

    // Components
    // =========================================================================

    public function getField(): FieldService
    {
        return $this->field;
    }

    public function geoLocation(): GeoLocationService
    {
        return $this->geoLocation;
    }
}
