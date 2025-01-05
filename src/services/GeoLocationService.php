<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
use craft\base\Event;
use craft\elements\Address;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\events\RegisterGeocoderEvent;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class GeoLocationService extends Component
{
    public $settings;

    public const EVENT_REGISTER_GEOCODERS = 'registerGeoCodersEvent';
    public $geoCoders = [];

    public function init(): void
    {
        $this->settings = EasyAddressField::getInstance()->getSettings();

        $event = new RegisterGeocoderEvent();
        Event::trigger(self::class, self::EVENT_REGISTER_GEOCODERS, $event);

        $this->geoCoders = collect(array_merge($this->geoCoders, $event->geoCoders))->map(function($geoCoder) {
            return \Craft::createObject($geoCoder);
        });

        parent::init();
    }

    /**
     * @param EasyAddressFieldModel $model
     *
     * @return EasyAddressFieldModel
     */
    public function locate(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        try {
            if (!$model->latitude && !$model->longitude and strlen($model->toString()) >= 2) {
                return $this->geoCoders[$this->settings->geoCodingService]->geocodeModel($model);
            }
            return $model;
        } catch (\Throwable $exception) {
            \Craft::error($exception->getMessage(), 'easy-address-field');
            return $model;
        }
    }

    /**
     * @param Address $element
     * @return Address
     */
    public function locateElement(Address $element): Address
    {
        try {
            if (!$element->latitude && !$element->longitude && $element->countryCode) {
                return $this->geoCoders[$this->settings->geoCodingService]->geocodeElement($element);
            }
            return $element;
        } catch (\Throwable $exception) {
            \Craft::error($exception->getMessage(), 'easy-address-field');
            return $element;
        }
    }
}
