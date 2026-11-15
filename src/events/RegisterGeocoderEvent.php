<?php

namespace studioespresso\easyaddressfield\events;

use studioespresso\easyaddressfield\services\geocoders\BaseGeoCoder;

/**
 * Dispatched when the geocoders are collected. Listen with `Event::listen(RegisterGeocoderEvent::class, ...)`.
 */
class RegisterGeocoderEvent
{
    /** @param array<string, class-string<BaseGeoCoder>> $geoCoders Geocoder classes keyed by handle */
    public function __construct(public array $geoCoders = []) {}
}
