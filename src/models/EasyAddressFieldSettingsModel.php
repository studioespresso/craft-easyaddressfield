<?php

namespace studioespresso\easyaddressfield\models;

use craft\base\model;

/**
 * Class EasyAddressFieldSettingsModel
 *
 * @package \studioespresso\easyaddressfield\models
 */
class EasyAddressFieldSettingsModel extends Model
{
    public $geocoder = "google";

    public $googleApiKey;
    public $googleApiKeyNonGeo;

    public $defaultMapTheme;

    public $defaultMarkerColor;

    public $defaultMarkerIcon;

}