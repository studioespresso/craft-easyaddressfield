<?php

namespace studioespresso\easyaddressfield\services;

use CraftCms\Cms\Address\Elements\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\events\RegisterGeocoderEvent;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\services\geocoders\BaseGeoCoder;
use studioespresso\easyaddressfield\services\geocoders\GoogleGeoCoder;
use studioespresso\easyaddressfield\services\geocoders\NomanatimGeoCoder;
use Throwable;

class GeoLocationService
{
    /** @var Collection<string, BaseGeoCoder> */
    public Collection $geoCoders { get => $this->geoCoders ??= $this->registerGeoCoders(); }

    public function locate(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        try {
            if (! $model->latitude && ! $model->longitude && strlen($model->toString()) >= 2) {
                return $this->geoCoder()?->geocodeModel($model) ?? $model;
            }
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), ['plugin' => 'easy-address-field']);
        }

        return $model;
    }

    public function locateElement(Address $element): Address
    {
        try {
            if (! $element->latitude && ! $element->longitude && $element->countryCode) {
                return $this->geoCoder()?->geocodeElement($element) ?? $element;
            }
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), ['plugin' => 'easy-address-field']);
        }

        return $element;
    }

    private function geoCoder(): ?BaseGeoCoder
    {
        return $this->geoCoders->get(EasyAddressField::getInstance()->getSettings()->geoCodingService);
    }

    /** @return Collection<string, BaseGeoCoder> */
    private function registerGeoCoders(): Collection
    {
        event($event = new RegisterGeocoderEvent([
            'nomanatim' => NomanatimGeoCoder::class,
            'google' => GoogleGeoCoder::class,
        ]));

        return collect($event->geoCoders)->map(fn (string $class) => app($class));
    }
}
