<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use League\ISO3166\ISO3166;
use studioespresso\easyaddressfield\Plugin;
use yii\web\View;

class CountriesService extends Component {

	public function getCountriesAsArray() {
		$data      = new ISO3166();
		$data      = $data->all();
		$countries = array();
		foreach ( $data as $country ) {
			$countries[ $country['alpha2'] ] = $country['name'];
		};

		return $countries;
	}
}