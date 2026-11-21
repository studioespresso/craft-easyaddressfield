<?php

namespace studioespresso\easyaddressfield\Services\GeoCoders;

use CraftCms\Cms\Address\Elements\Address;
use Illuminate\Support\Facades\Http;
use studioespresso\easyaddressfield\Data\EasyAddressFieldModel;

class NomanatimGeoCoder extends BaseGeoCoder
{
    public ?string $name = 'Nomanatim';

    public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        $result = $this->makeApiCall([
            'countrycodes' => $model->country,
            'state' => $model->state,
            'city' => $model->city,
            'postalcode' => $model->postalCode,
            'street' => trim($model->street.' '.$model->street2),
        ]);

        if ($result) {
            $model->latitude = $result['latitude'];
            $model->longitude = $result['longitude'];
        }

        return $model;
    }

    public function geocodeElement(Address $element): Address
    {
        $result = $this->makeApiCall([
            'countrycodes' => $element->countryCode,
            'state' => $element->administrativeArea,
            'postalcode' => $element->postalCode,
            'street' => trim($element->addressLine1.' '.$element->addressLine2.' '.$element->addressLine3),
        ]);

        if ($result) {
            $element->latitude = $result['latitude'];
            $element->longitude = $result['longitude'];
        }

        return $element;
    }

    /**
     * @param  array<string, string|null>  $query
     * @return array{latitude: string, longitude: string}|null
     */
    private function makeApiCall(array $query): ?array
    {
        // Nominatim's usage policy requires an identifying User-Agent
        $result = Http::timeout(10)
            ->withUserAgent('Easy Address Field for Craft CMS ('.config('app.url').')')
            ->get('https://nominatim.openstreetmap.org/search', array_filter($query) + ['format' => 'jsonv2', 'limit' => 1])
            ->json();

        if (! isset($result[0]['lat'], $result[0]['lon'])) {
            return null;
        }

        return ['latitude' => (string) $result[0]['lat'], 'longitude' => (string) $result[0]['lon']];
    }
}
