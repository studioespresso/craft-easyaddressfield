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
    public $googleApiKey;
    public $googleApiKeyNonGeo;
    public $geoCodingService;
    public $defaultMapTheme;
    public $defaultMarkerColor;
    public $defaultMarkerIcon;

}