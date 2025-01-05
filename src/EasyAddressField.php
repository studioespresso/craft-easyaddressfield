<?php
/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Address;
use craft\events\ModelEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use markhuot\CraftQL\Events\GetFieldSchema;
use studioespresso\easyaddressfield\events\RegisterGeocoderEvent;
use studioespresso\easyaddressfield\fields\EasyAddressFieldFeedMe;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;
use studioespresso\easyaddressfield\services\CountriesService;
use studioespresso\easyaddressfield\services\FieldService;
use studioespresso\easyaddressfield\services\geocoders\GoogleGeoCoder;
use studioespresso\easyaddressfield\services\geocoders\NomanatimGeoCoder;
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
    public bool $hasCpSettings = true;

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

        Event::on(Address::class, Element::EVENT_BEFORE_SAVE, function(ModelEvent $event) {
            /* @var Address $element */
            $element = $event->sender;
            if (ElementHelper::isDraftOrRevision($element)) {
                return;
            }
            if ($this->getSettings()->enableGeoCodingForCraftElements) {
                $event->sender = $this->geoLocation()->locateElement($element);
            }
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

        Event::on(GeoLocationService::class, GeoLocationService::EVENT_REGISTER_GEOCODERS, function(RegisterGeocoderEvent $event) {
            $event->geoCoders['nomanatim'] = NomanatimGeoCoder::class;
            $event->geoCoders['google'] = GoogleGeoCoder::class;
        });
    }

    // Components
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel(): Model
    {
        return new EasyAddressFieldSettingsModel();
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     * @throws \Twig_Error_Loader
     * @throws \RuntimeException
     */
    protected function settingsHtml(): string
    {
        $geoCoders = EasyAddressField::getInstance()->geoLocation->geoCoders;
        $geoCoders = $geoCoders->map(function($item) {
            return $item->name;
        });

        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_settings',
            [
                'geoCoders' => $geoCoders->toArray(),
                'settings' => $this->getSettings(),
            ]
        );
    }

    /**
     * Redirect to settings after install
     */
    protected function afterInstall(): void
    {
        if (!Craft::$app->getRequest()->isConsoleRequest) {
            parent::afterInstall();
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/easy-address-field'))->send();
        }
    }

    public function getField(): FieldService
    {
        return $this->field;
    }

    public function geoLocation(): GeoLocationService
    {
        return $this->geoLocation;
    }
}
