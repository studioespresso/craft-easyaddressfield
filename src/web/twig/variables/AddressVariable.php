<?php

namespace studioespresso\easyaddressfield\web\twig\variables;

use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\services\CountriesService;

class AddressVariable
{
    private $settings;

    public function __construct()
    {
        $pluginSettings = EasyAddressField::getInstance()->getSettings();
        $this->settings = $pluginSettings;
    }

    public function countries()
    {
        $countriesService = new CountriesService();

        return $countriesService->getCountriesAsArray();
    }
}
