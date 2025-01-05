<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use Craft;
use craft\base\Component;
use craft\elements\Address;
use craft\helpers\Json;
use GuzzleHttp\Client;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use yii\base\InvalidConfigException;

class GoogleGeoCoder extends BaseGeoCoder
{

    /**
     * Label for the geocoder, displayed in the plugin's settings
     * @var string|null
     */
    public ?string $name = "Google";

    /**
     * This function is used to geocode the model from the EasyAddressField
     * @param EasyAddressFieldModel $model
     * @return mixed
     */
    public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        if (!$this->settings->googleApiKey) {
            return $model;
        }

        if (!$model->latitude && !$model->longitude and strlen($model->toString()) >= 2) {
            $result = $this->makeApiCall($model->toString());
            if ($result === false) {
                return $model;
            }

            if ($result) {
                $model->latitude = $result['latitude'];
                $model->longitude = $result['longitude'];
                return $model;
            }
        }

        return $model;
    }

    /**
     * This function is used to geocode a Craft Address element
     * @param Address $element
     * @return mixed
     */
    public function geocodeElement(Address $element): Address
    {
        $fields = [
            $element->addressLine1,
            $element->addressLine2,
            $element->addressLine3,
            $element->postalCode,
            $element->locality,
            $element->countryCode
        ];
        $fields = array_filter($fields);
        $data = implode('+', $fields);
        $result = $this->makeApiCall($data);
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
        $client = new Client(['base_uri' => 'https://maps.googleapis.com']);
        $request = $client->request('GET',
            'maps/api/geocode/json?address=' . urlencode($data) . '&key=' . Craft::parseEnv($this->settings->googleApiKey) . '',
            ['allow_redirects' => false]
        );
        $result = Json::decodeIfJson($request->getBody()->getContents());

        if ($result['status'] !== 'OK' && $result['error_message']) {
            if (Craft::$app->getConfig()->general->devMode) {
                throw new InvalidConfigException('Google API error: ' . $result['error_message']);
            }
            Craft::error($result['error_message'], 'easy-address-field');
        }
        if ($result['status'] === 'OK') {
            if ($result['status'] === 'OK') {
                if ($result['results'][0]['geometry']['location']) {
                    $data = [
                        'latitude' => $result['results'][0]['geometry']['location']['lat'],
                        'longitude' => $result['results'][0]['geometry']['location']['lng'],
                    ];
                    return $data;
                }
            }
        }
        return false;
    }
}