<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
use GuzzleHttp\Client;

class GeoLocationService extends Component {


	public function geoLocate( $value )
	{

		//$settings = craft()->plugins->getPlugin( 'easyAddressField' )->getSettings();

		$client = new Client( [ 'base_uri' => 'https://maps.googleapis.com' ] );

		$res    = $client->request( 'GET', 'maps/api/geocode/json?address=' . urlencode( implode( '+', $value->toString() )
			) . '&key=AIzaSyAnif1BOqZDa_jc143tyiR6xFw8OuWtuBE', [ 'allow_redirects' => false ] );
		$json   = json_decode( $res->getBody()->getContents(), true );


		if ( $json['results'][0]['geometry']['location'] ) {
			$value['latitude'] = $json['results'][0]['geometry']['location']['lat'];
			$value['longitude'] = $json['results'][0]['geometry']['location']['lng'];
		}

		return $value;
	}

}