<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use Craft;
use craft\base\Component;
use craft\elements\Address;
use craft\helpers\Json;
use GuzzleHttp\Client;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use yii\base\InvalidConfigException;

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
        return $model;
    }

    /**
     * This function is used to geocode a Craft Address element
     * @param Address $element
     * @return mixed
     */
    public function geocodeElement(Address $element): Address
    {

        return $element;
    }

    private function makeApiCall($data): array|false
    {

    }
}