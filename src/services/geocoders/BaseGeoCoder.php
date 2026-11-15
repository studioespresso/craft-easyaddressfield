<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use CraftCms\Cms\Address\Elements\Address;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;

abstract class BaseGeoCoder
{
    /** Label for the geocoder, displayed in the plugin's settings */
    public ?string $name = null;

    public EasyAddressFieldSettingsModel $settings { get => EasyAddressField::getInstance()->getSettings(); }

    /**
     * Geocodes the value of an Easy Address Field
     */
    abstract public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel;

    /**
     * Geocodes a Craft Address element
     */
    abstract public function geocodeElement(Address $element): Address;
}
