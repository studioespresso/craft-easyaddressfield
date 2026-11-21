<?php

namespace studioespresso\easyaddressfield\Services\GeoCoders;

use CraftCms\Cms\Address\Elements\Address;
use studioespresso\easyaddressfield\Data\EasyAddressFieldModel;
use studioespresso\easyaddressfield\EasyAddressFieldSettingsModel;

abstract class BaseGeoCoder
{
    /** Label for the geocoder, displayed in the plugin's settings */
    public ?string $name = null;

    public EasyAddressFieldSettingsModel $settings { get => app(EasyAddressFieldSettingsModel::class); }

    /**
     * Geocodes the value of an Easy Address Field
     */
    abstract public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel;

    /**
     * Geocodes a Craft Address element
     */
    abstract public function geocodeElement(Address $element): Address;
}
