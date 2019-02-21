<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
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
            $countries[$country['alpha2']] = $country['name'];
        };

        return $countries;
    }

    public function getCountryNameByAlpha2($code)
    {
        $data = new ISO3166();
        $country = $data->alpha2($code);
        return $country['name'];
    }
}