<?php

namespace studioespresso\easyaddressfield\services;

use craft\base\Component;
use GuzzleHttp\Client;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;

class GeoLocationService extends Component {

	/**
	 * @param EasyAddressFieldModel $model
	 *
	 * @return EasyAddressFieldModel
	 */
	public function locate( EasyAddressFieldModel $model ) {
		$pluginSettings = EasyAddressField::getInstance()->getSettings();
		if ( ! $pluginSettings->googleApiKey ) {
			return $value;
		}

		if ( ! $model->latitude && ! $model->longitude ) {

			$client = new Client( [ 'base_uri' => 'https://maps.googleapis.com' ] );
			$res    = $client->request( 'GET', 'maps/api/geocode/json?address=' . urlencode( $model->toString() ) . '&key=' . $pluginSettings->googleApiKey . '', [ 'allow_redirects' => false ] );
			$json   = json_decode( $res->getBody()->getContents(), true );

			if ( $json['status'] == 'OK' ) {
				if ( $json['results'][0]['geometry']['location'] ) {
					$model->latitude  = $json['results'][0]['geometry']['location']['lat'];
					$model->longitude = $json['results'][0]['geometry']['location']['lng'];
				}
			}
		}

		return $model;

	}

}