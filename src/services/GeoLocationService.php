<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use maxh\Nominatim\Nominatim;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use yii\base\InvalidConfigException;
use yii\log\Logger;

class GeoLocationService extends Component
{
    public $settings;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->settings = EasyAddressField::getInstance()->getSettings();
    }

    /**
     * @param EasyAddressFieldModel $model
     *
     * @return EasyAddressFieldModel
     */
    public function locate(EasyAddressFieldModel $model)
    {
        if (!$model->latitude && !$model->longitude and strlen($model->toString()) >= 2) {
            $model = $this->geocodeOSM($model);
        }

        return $model;
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
        if(empty($result)) {
            return $model;
        }

        if(isset($result[0]['lat']) && isset($result[0]['lon'])) {
            $model->longitude = $result[0]['lon'];
            $model->latitude = $result[0]['lat'];
        } elseif(is_array($result[0]['geojson']['coordinates'][0]) && is_array($result[0]['geojson']['coordinates'][0][0])) {
            $model->longitude = $result[0]['geojson']['coordinates'][0][0][0];
            $model->latitude = $result[0]['geojson']['coordinates'][0][0][1];
        } elseif(is_array($result[0]['geojson']['coordinates'][0])) {
            $model->longitude = $result[0]['geojson']['coordinates'][0][0];
            $model->latitude = $result[0]['geojson']['coordinates'][0][1];
        } else {
            $model->longitude = $result[0]['geojson']['coordinates'][0];
            $model->latitude = $result[0]['geojson']['coordinates'][1];
        }
        return $model;
    }

}