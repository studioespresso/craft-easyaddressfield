<?php

namespace studioespresso\easyaddressfield;

use CraftCms\Cms\Plugin\PluginSettings;

class EasyAddressFieldSettingsModel extends PluginSettings
{
    public string $geoCodingService = 'nomanatim';

    public ?string $googleApiKey = null;

    public bool $enableGeoCodingForCraftElements = false;

    public function getRules(): array
    {
        return [
            'geoCodingService' => ['required', 'string'],
            'googleApiKey' => ['nullable', 'string'],
            'enableGeoCodingForCraftElements' => ['boolean'],
        ];
    }
}
