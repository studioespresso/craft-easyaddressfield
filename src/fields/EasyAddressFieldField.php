<?php

namespace studioespresso\easyaddressfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\helpers\Db;
use craft\helpers\ElementHelper;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldAsset;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\graphql\EasyAddressFieldTypeGenerator;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class EasyAddressFieldField extends Field implements PreviewableFieldInterface
{
    public $hasContentColumn = false;

    public $geoCode = true;

    public $showCoordinates = false;

    public $defaultCountry;

    public $enabledFields = array(
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
        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_field/_settings',
            [
                'field' => $this,
                'countries' => EasyAddressField::getInstance()->countries->getCountriesAsArray(),
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
                        'geoCode',
                    ),
                    'boolean',
                ),
                array(
                    array(
                        'showCoordinates',
                    ),
                    'boolean',
                ),
                array(
                    array(
                        'defaultCountry',
                    ),
                    'string',
                ),
            );


        $rules = parent::rules();
        $rules = array_merge($rules, $addressRules);

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public static function dbType(): null
    {
        return null;
    }

    public function getContentGqlType(): array
    {
        $typeArray = EasyAddressFieldTypeGenerator::generateTypes($this);

        return [
            'name' => $this->handle,
            'description' => "Easy Address Field field",
            'type' => array_shift($typeArray),
        ];
    }


    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     *
     * @return mixed|EasyAddressFieldModel
     */
    public function normalizeValue($value, ElementInterface $element = null): mixed
    {
        return EasyAddressField::$plugin->getField()->getField($this, $element, $value);
    }

    /**
     * @param $value
     * @param ElementInterface|null $element
     * @return array|mixed|null|string
     */
    public function serializeValue($value, ElementInterface $element = null): mixed
    {
        $settings = $this->getSettings();
        if (!$value) {
            return $value;
        }
        if (!ElementHelper::isDraftOrRevision($element)) {
            if ($settings['geoCode'] and empty($value['latitude']) and empty($value['longitude'])) {
                $value = EasyAddressField::getInstance()->geoLocation->locate($value);
            }
        }

        return Db::prepareValueForDb($value);
    }

    public function isValueEmpty($value, ElementInterface $element): bool
    {
        return $value->isEmpty();
    }

    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        return $value->modelToString(', ');
    }

    public function getSearchKeywords($value, ElementInterface $element): string
    {
        return $value->modelToString(' ');
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

        return Craft::$app->getView()->renderTemplate(
            'easy-address-field/_field/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'countries' => EasyAddressField::getInstance()->countries->getCountriesAsArray(),
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
    public function afterElementSave(ElementInterface $element, bool $isNew): void
    {
        EasyAddressField::getInstance()->field->saveField($this, $element);
        parent::afterElementSave($element, $isNew);
    }
}
