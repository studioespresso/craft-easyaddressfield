<?php

namespace studioespresso\easyaddressfield\services\geocoders;

use CraftCms\Cms\Address\Elements\Address;
use CraftCms\Cms\Support\Env;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class GoogleGeoCoder extends BaseGeoCoder
{
    public ?string $name = 'Google';

    public function geocodeModel(EasyAddressFieldModel $model): EasyAddressFieldModel
    {
        if ($result = $this->makeApiCall($model->toString())) {
            $model->latitude = $result['latitude'];
            $model->longitude = $result['longitude'];
        }

        return $model;
    }

    public function geocodeElement(Address $element): Address
    {
        $address = implode('+', array_filter([
            $element->addressLine1,
            $element->addressLine2,
            $element->addressLine3,
            $element->postalCode,
            $element->locality,
            $element->countryCode,
        ]));

        if ($result = $this->makeApiCall($address)) {
            $element->latitude = $result['latitude'];
            $element->longitude = $result['longitude'];
        }

        return $element;
    }

    /** @return array{latitude: string, longitude: string}|null */
    private function makeApiCall(string $address): ?array
    {
        $apiKey = Env::parse($this->settings->googleApiKey);
        if (! $apiKey) {
            return null;
        }

        $result = Http::timeout(10)
            ->withoutRedirecting()
            ->get('https://maps.googleapis.com/maps/api/geocode/json', ['address' => $address, 'key' => $apiKey])
            ->json();

        if (($result['status'] ?? null) === 'OK' && isset($result['results'][0]['geometry']['location'])) {
            $location = $result['results'][0]['geometry']['location'];

            return ['latitude' => (string) $location['lat'], 'longitude' => (string) $location['lng']];
        }

        if ($message = $result['error_message'] ?? null) {
            if (config('app.debug')) {
                throw new RuntimeException('Google API error: '.$message);
            }
            Log::error($message, ['plugin' => 'easy-address-field']);
        }

        return null;
    }
}
