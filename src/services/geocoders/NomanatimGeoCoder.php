<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use craft\elements\Address;
use maxh\Nominatim\Nominatim;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class NomanatimGeoCoder extends BaseGeoCoder
{
    /**
     * Label for the geocoder, displayed in the plugin's settings
     * @var string|null
     */
    public ?string $name = "Nomanatim";

    /**
     * This function is used to geocode the model from the EasyAddressField
     * @param EasyAddressFieldModel $model
     * @return mixed
     */
    public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        $data = [
            'country' => $model->countryCode,
            'state' => $model->state,
            'city' => $model->city,
            'postalCode' => $model->postalCode,
            'street' => $model->street,
            'street2' => $model->street2,
        ];

        $result = $this->makeApiCall($data);
        if (empty($result)) {
            return $model;
        }

        $model->latitude = $result['latitude'];
        $model->longitude = $result['longitude'];

        return $model;
    }

    /**
     * This function is used to geocode a Craft Address element
     * @param Address $element
     * @return mixed
     */
    public function geocodeElement(Address $element): Address
    {
        $data = [
            'country' => $element->countryCode,
            'state' => $element->state,
            'city' => '',
            'postalCode' => $element->postalCode,
            'street' => $element->addressLine1,
            'street2' => $element->addressLine2 . ' ' . $element->addressLine3,
        ];

        $result = $this->makeApiCall($data);
        if (empty($result)) {
            return $element;
        }

        if ($result) {
            $element->setAttributes([
                'longitude' => $result['longitude'],
                'latitude' => $result['latitude'],
            ]);
        }

        return $element;
    }

    private function makeApiCall($data): array|false
    {
        // url encode the address
        $url = "http://nominatim.openstreetmap.org/";
        $nominatim = new Nominatim($url);
        $search = $nominatim->newSearch()
            ->countryCode($data['country'])
            ->state($data['state'] ?? '')
            ->city($data['city'] ?? '')
            ->postalCode($data['postalCode'] ?? '')
            ->street($data['street'] . ' ' . $data['street2'])
            ->limit(1)
            ->polygon('geojson')
            ->addressDetails();

        $result = $nominatim->find($search);
        if (empty($result)) {
            return [];
        }

        if (isset($result[0]['lat']) && isset($result[0]['lon'])) {
            return [
                'latitude' => $result[0]['lat'],
                'longitude' => $result[0]['lon'],
            ];
        }

        if (is_array($result[0]['geojson']['coordinates'][0]) && is_array($result[0]['geojson']['coordinates'][0][0])) {
            return [
                'latitude' => $result[0]['geojson']['coordinates'][0][0][1],
                'longitude' => $result[0]['geojson']['coordinates'][0][0][0],
            ];
        }

        if (is_array($result[0]['geojson']['coordinates'][0])) {
            return [
                'latitude' => $result[0]['geojson']['coordinates'][0][1],
                'longitude' => $result[0]['geojson']['coordinates'][0][0],
            ];
        }

        return [
            'latitude' => $result[0]['geojson']['coordinates'][1],
            'longitude' => $result[0]['geojson']['coordinates'][0],
        ];
    }
}
