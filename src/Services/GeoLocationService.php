<?php

namespace studioespresso\easyaddressfield\Services;

use CraftCms\Cms\Address\Elements\Address;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use studioespresso\easyaddressfield\Data\EasyAddressFieldModel;
use studioespresso\easyaddressfield\EasyAddressFieldSettingsModel;
use studioespresso\easyaddressfield\Events\RegisterGeocoderEvent;
use studioespresso\easyaddressfield\Services\GeoCoders\BaseGeoCoder;
use studioespresso\easyaddressfield\Services\GeoCoders\GoogleGeoCoder;
use studioespresso\easyaddressfield\Services\GeoCoders\NomanatimGeoCoder;
use Throwable;

#[Singleton]
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
        return $this->geoCoders->get(app(EasyAddressFieldSettingsModel::class)->geoCodingService);
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
