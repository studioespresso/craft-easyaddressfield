<?php

namespace studioespresso\easyaddressfield\models;

use yii\base\Model;


class EasyAddressFieldModel extends Model {

	public function rules() {
		return [
			[
				[
					'street',
					'street',
					'postalCode',
					'latitude',
					'longitude',
				],
				'safe',
			],
		];
	}

	/**
	 * Latitude
	 *
	 * @var string
	 */
	public $latitude;

	/**
	 * @return string
	 */
	public function getLatitude(): string {
		return $this->latitude;
	}

	/**
	 * Longitude
	 *
	 * @var string
	 */
	public $longitude;

	/**
	 * @return string
	 */
	public function getLongitude(): string {
		return $this->longitude;
	}

	/**
	 * The postal code.
	 *
	 * @var string
	 */
	public $postalCode;

	/**
	 * The first line of the street block.
	 *
	 * @var string
	 */
	public $street;

	/**
	 * The second line of the street block.
	 *
	 * @var string
	 */
	public $street2;

}