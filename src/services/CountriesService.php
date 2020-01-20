<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use Giggsey\Locale\Locale;
use League\ISO3166\ISO3166;

class CountriesService extends Component
{

    /**
     * @return array
     */
    public function getCountriesAsArray()
    {
        $data = new ISO3166();
        $data = $data->all();
        $countries = array();
        foreach ($data as $country) {
            $countries[$country['alpha2']] = Locale::getDisplayRegion('-' . $country['alpha2'], Craft::$app->getLocale());
        };
        asort($countries);
        
        return $countries;
    }

    public function getCountryNameByAlpha2($code, $locale)
    {
        $data = new ISO3166();
        $country = $data->alpha2($code);

        try {
            $translatedLocale = Locale::getDisplayRegion('-' . $country['alpha2'], $locale);
            return $translatedLocale;
        } catch (\Exception $e) {
            return $country['name'];
        }
    }
}