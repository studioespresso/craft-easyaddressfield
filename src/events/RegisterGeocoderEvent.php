<?php

namespace studioespresso\easyaddressfield\events;

use craft\base\Event;

class RegisterGeocoderEvent extends Event
{
    public $geoCoders = [];
}