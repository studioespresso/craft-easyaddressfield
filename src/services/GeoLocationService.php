<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
use GuzzleHttp\Client;
use studioespresso\easyaddressfield\EasyAddressField;

class GeoLocationService extends Component
{


    public function geoLocate($value)
    {
        $pluginSettings = EasyAddressField::getInstance()->getSettings();
        if (!$pluginSettings->googleApiKey) {
            return $value;
        }
        $apiKey = $pluginSettings->googleApiKey;
        $client = new Client(['base_uri' => 'https://maps.googleapis.com']);
        $res = $client->request('GET', 'maps/api/geocode/json?address=' . urlencode($value->toString()) . '&key='. $apiKey .'', ['allow_redirects' => false]);
        $json = json_decode($res->getBody()->getContents(), true);

	    if ( $json['status'] == 'OK' ) {
		    if ( $json['results'][0]['geometry']['location'] ) {
			    $value['latitude']  = $json['results'][0]['geometry']['location']['lat'];
			    $value['longitude'] = $json['results'][0]['geometry']['location']['lng'];
		    }
	    }

        return $value;
    }

}