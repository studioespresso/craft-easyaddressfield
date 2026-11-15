<?php

namespace studioespresso\easyaddressfield\services;

use Giggsey\Locale\Locale;
use League\ISO3166\ISO3166;
use Throwable;

class CountriesService
{
    /**
     * @return array<string, string> Country names keyed by alpha-2 code, sorted by name
     */
    public function getCountriesAsArray(): array
    {
        $locale = app()->getLocale();
        $countries = [];
        foreach ((new ISO3166)->all() as $country) {
            $countries[$country['alpha2']] = Locale::getDisplayRegion('-'.$country['alpha2'], $locale) ?: $country['name'];
        }
        asort($countries);

        return $countries;
    }

    public function getCountryNameByAlpha2(string $code, string $locale): ?string
    {
        try {
            $country = (new ISO3166)->alpha2($code);
        } catch (Throwable) {
            return null;
        }

        try {
            return Locale::getDisplayRegion('-'.$country['alpha2'], $locale) ?: $country['name'];
        } catch (Throwable) {
            return $country['name'];
        }
    }
}
