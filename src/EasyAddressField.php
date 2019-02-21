<?php
/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use markhuot\CraftQL\Events\GetFieldSchema;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldSettignsAsset;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;
use studioespresso\easyaddressfield\services\CountriesService;
use studioespresso\easyaddressfield\services\FieldService;
use studioespresso\easyaddressfield\services\GeoLocationService;
use studioespresso\easyaddressfield\web\twig\variables\AddressVariable;
use yii\base\Event;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use yii\web\UrlManager;

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
    public $hasCpSettings = true;

    // Public Methods
    // =========================================================================
    public function init()
    {
        self::$plugin = $this;

        $this->setComponents([
            'field' => FieldService::class,
            'geolocation' => GeoLocationService::class,
            'countries' => CountriesService::class
        ]);

        // Register our fields
        Event::on(Fields::className(), Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = EasyAddressFieldField::class;
        });

        // Register our twig functions
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $variable = $event->sender;
            $variable->set('address', AddressVariable::class);
        });

        Event::on(EasyAddressFieldField::class, 'craftQlGetFieldSchema', function (GetFieldSchema $event) {
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
    }

    // Components
    // =========================================================================

    public function getField(): FieldService
    {
        return $this->field;
    }

    public function geolocation(): GeoLocationService
    {
        return $this->geolocation;
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new EasyAddressFieldSettingsModel();
    }

    /**
     * Redirect to settings after install
     */
    protected function afterInstall()
    {
        if(!Craft::$app->getRequest()->isConsoleRequest) {
            parent::afterInstall();
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/easy-address-field'))->send();
        }
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     * @throws \Twig_Error_Loader
     * @throws \RuntimeException
     */
    protected function settingsHtml(): string
    {
        $url = Craft::$app->assetManager->getPublishedUrl('@studioespresso/easyaddressfield/assets/themes', true);
        $styleOptions = [
            'standard' => Craft::t("easy-address-field", "Standard"),
            'silver' => Craft::t("easy-address-field", 'Silver'),
            'retro' => Craft::t("easy-address-field", 'Retro'),
            'dark' => Craft::t("easy-address-field", 'Dark'),
            'night' => Craft::t("easy-address-field", 'Night'),
            'aubergine' => Craft::t("easy-address-field", 'Aubergine'),
        ];

        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_settings',
            [
                'settings' => $this->getSettings(),
                'styleOptions' => $styleOptions,
                'url' => $url,
            ]
        );
    }
}
