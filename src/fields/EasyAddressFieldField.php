<?php

namespace studioespresso\easyaddressfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Db;
use League\ISO3166\ISO3166;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldAsset;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\Plugin;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\services\CountriesService;
use studioespresso\easyaddressfield\services\GeoLocationService;
use yii\db\Schema;


class EasyAddressFieldField extends Field implements PreviewableFieldInterface
{

    public $hasContentColumn = false;

    public $geoCode = true;

    public $showCoordinates = false;

    public $defaultCountry;

    public $fields = array(
        'name' => false,
        'street' => true,
        'street2' => false,
        'postalCode' => true,
        'city' => true,
        'state' => false,
        'country' => true,
    );

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('easy-address-field', 'Easy Address Field');
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     * @throws \Twig_Error_Loader
     * @throws \RuntimeException
     */
    public function getSettingsHtml(): string
    {
        // Render the settings template
        $countriesService = new CountriesService();
        $countries = $countriesService->getCountriesAsArray();

        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_field/_settings',
            [
                'field' => $this,
                'countries' => $countries
            ]
        );
    }

    /**
     * @return array
     */
    public function rules(): array
    {

        $addressRules =
            array(
                array(
                    array(
                        'geoCode'
                    ),
                    'boolean'
                ),
                array(
                    array(
                        'showCoordinates'
                    ),
                    'boolean'
                ),
                array(
                    array(
                        'defaultCountry'
                    ),
                    'string'
                )
            );


        $rules = parent::rules();
        $rules = array_merge($rules, $addressRules);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     *
     * @return mixed|EasyAddressFieldModel
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return EasyAddressField::$plugin->getField()->getField($this, $element, $value);

    }

    /**
     * @param $value
     * @param ElementInterface|null $element
     * @return array|mixed|null|string
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        $settings = $this->getSettings();
        if (!$value) {
            return $value;
        }

        if ($settings['geoCode'] and empty($value['latitude']) and empty($value['longitude'])) {
            $service = new GeoLocationService();
            $value = $service->geoLocate($value);

        }

        return Db::prepareValueForDb($value);
    }


    /**
     * @param $value
     * @param ElementInterface|null $element
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(EasyAddressFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        $pluginSettings = EasyAddressField::getInstance()->getSettings();
        $fieldSettings = $this->getSettings();

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        $fieldSettings = $this->getSettings();

        $keyConfigured = false;

        $iconUrl = Craft::$app->assetManager->getPublishedUrl('@studioespresso/easyaddressfield/assets/icon', true,
            'marker.svg');

        if ($pluginSettings->googleApiKey) {
            Craft::$app->getView()->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . $pluginSettings->googleApiKey);
            $keyConfigured = true;
        }

        $countriesService = new CountriesService();
        $countries = $countriesService->getCountriesAsArray();

        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_field/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'keyConfigured' => $keyConfigured,
                'iconUrl' => $iconUrl,
                'countries' => $countries,
                'namespacedId' => $namespacedId,
                'fieldSettings' => $fieldSettings,
                'pluginSettings' => $pluginSettings,
            ]
        );
    }

    /**
     * @param ElementInterface $element
     * @param bool $isNew
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        EasyAddressField::$plugin->getField()->saveField($this, $element);
        parent::afterElementSave($element, $isNew);
    }

}