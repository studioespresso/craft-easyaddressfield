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
    public string $geoCodingService = 'nomanatim';

    public ?string $googleApiKey = null;

    public bool $enableGeoCodingForCraftElements = true;
}
