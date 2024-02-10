<?php

namespace studioespresso\easyaddressfield\models;

use craft\base\Model;

/**
 * Class EasyAddressFieldSettingsModel
 *
 * @package \studioespresso\easyaddressfield\models
 */
class EasyAddressFieldSettingsModel extends Model
{
    public $googleApiKey;
    public $googleApiKeyNonGeo;
    public $defaultMapTheme;
    public $defaultMarkerColor;
    public $defaultMarkerIcon;
}
