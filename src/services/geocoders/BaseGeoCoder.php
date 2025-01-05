<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use craft\base\Component;
use craft\elements\Address;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;

abstract class BaseGeoCoder extends Component
{
    public EasyAddressFieldSettingsModel $settings;

    public ?string $name = null;

    public function init(): void
    {
        $this->settings = EasyAddressField::getInstance()->getSettings();
        parent::init();
    }

    /**
     * This function is used to geocode the model from the EasyAddressField
     * @param EasyAddressFieldModel $model
     * @return mixed
     */
    abstract public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel;

    /**
     * This function is used to geocode a Craft Address element
     * @param Address $element
     * @return mixed
     */
    abstract public function geocodeElement(Address $element): Address;
}
