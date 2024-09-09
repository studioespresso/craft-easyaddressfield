<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
use maxh\Nominatim\Nominatim;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class GeoLocationService extends Component
{
    /**
     * @param EasyAddressFieldModel $model
     *
     * @return EasyAddressFieldModel
     */
    public function locate(EasyAddressFieldModel $model)
    {
        try {
            if (!$model->latitude && !$model->longitude and strlen($model->toString()) >= 2) {
                $model = $this->geocodeOSM($model);
            }
            return $model;
        } catch (\Throwable $exception) {
            \Craft::error($exception->getMessage());
            return $model;
        }
    }

    private function geocodeOSM(EasyAddressFieldModel $model)
    {
        // url encode the address
        $url = "http://nominatim.openstreetmap.org/";
        $nominatim = new Nominatim($url);
        $search = $nominatim->newSearch()
            ->countryCode($model->country)
            ->state($model->state ?? '')
            ->city($model->city ?? '')
            ->postalCode($model->postalCode ?? '')
            ->street($model->street . ' ' . $model->street2)
            ->limit(1)
            ->polygon('geojson')
            ->addressDetails();

        $result = $nominatim->find($search);
        if (empty($result)) {
            return $model;
        }

        if (isset($result[0]['lat']) && isset($result[0]['lon'])) {
            $model->longitude = $result[0]['lon'];
            $model->latitude = $result[0]['lat'];
        } elseif (is_array($result[0]['geojson']['coordinates'][0]) && is_array($result[0]['geojson']['coordinates'][0][0])) {
            $model->longitude = $result[0]['geojson']['coordinates'][0][0][0];
            $model->latitude = $result[0]['geojson']['coordinates'][0][0][1];
        } elseif (is_array($result[0]['geojson']['coordinates'][0])) {
            $model->longitude = $result[0]['geojson']['coordinates'][0][0];
            $model->latitude = $result[0]['geojson']['coordinates'][0][1];
        } else {
            $model->longitude = $result[0]['geojson']['coordinates'][0];
            $model->latitude = $result[0]['geojson']['coordinates'][1];
        }
        return $model;
    }
}
