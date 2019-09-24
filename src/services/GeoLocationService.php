<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use yii\base\InvalidConfigException;
use yii\log\Logger;

class GeoLocationService extends Component
{

    /**
     * @param EasyAddressFieldModel $model
     *
     * @return EasyAddressFieldModel
     */
    public function locate(EasyAddressFieldModel $model)
    {
        $pluginSettings = EasyAddressField::getInstance()->getSettings();
        if (!$pluginSettings->googleApiKey) {
            return $model;
        }

        if (!$model->latitude && !$model->longitude and strlen($model->toString()) >= 2) {
            $client = new Client(['base_uri' => 'https://maps.googleapis.com']);
            $res = $client->request('GET', 'maps/api/geocode/json?address=' . urlencode($model->toString()) . '&key=' . Craft::parseEnv($pluginSettings->googleApiKey) . '', ['allow_redirects' => false]);
            $json = json_decode($res->getBody()->getContents(), true);

            $generalConfig = Craft::$app->getConfig();
            if ($json['status'] != 'OK' && $json['error_message']) {
                if ($generalConfig->general->devMode) {
                    throw new InvalidConfigException('Google API error: ' . $json['error_message']);
                }
                Craft::getLogger()->log($json['error_message'], Logger::LEVEL_ERROR, 'easy-address-field');
            }

            if ($json['status'] == 'OK') {
                if ($json['results'][0]['geometry']['location']) {
                    $model->latitude = $json['results'][0]['geometry']['location']['lat'];
                    $model->longitude = $json['results'][0]['geometry']['location']['lng'];
                }
            }
        }

        return $model;

    }

}