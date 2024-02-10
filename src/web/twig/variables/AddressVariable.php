<?php

namespace studioespresso\easyaddressfield\web\twig\variables;

use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\services\CountriesService;

class AddressVariable
{
    private $key;

    private $settings;

    public function __construct()
    {
        $pluginSettings = EasyAddressField::getInstance()->getSettings();
        $this->settings = $pluginSettings;
        $this->key = $pluginSettings->googleApiKeyNonGeo ? $pluginSettings->googleApiKeyNonGeo : $pluginSettings->googleApiKey;
    }

    public function countries()
    {
        $countriesService = new CountriesService();

        return $countriesService->getCountriesAsArray();
    }
}
