<?php

namespace studioespresso\easyaddressfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\helpers\Db;
use League\ISO3166\ISO3166;
use studioespresso\easyaddressfield\assetbundles\easyaddressfield\EasyAddressFieldAsset;
use studioespresso\easyaddressfield\Plugin;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\services\GeoLocationService;
use yii\db\Schema;


class EasyAddressFieldField extends Field implements PreviewableFieldInterface {

	protected $countryCodes = array(
		'AF',
		'AX',
		'AL',
		'DZ',
		'AS',
		'AD',
		'AO',
		'AI',
		'AQ',
		'AG',
		'AR',
		'AM',
		'AW',
		'AU',
		'AT',
		'AZ',
		'BS',
		'BH',
		'BD',
		'BB',
		'BY',
		'BE',
		'BZ',
		'BJ',
		'BM',
		'BT',
		'BO',
		'BQ',
		'BA',
		'BW',
		'BV',
		'BR',
		'IO',
		'BN',
		'BG',
		'BF',
		'BI',
		'KH',
		'CM',
		'CA',
		'CV',
		'KY',
		'CF',
		'TD',
		'CL',
		'CN',
		'CX',
		'CC',
		'CO',
		'KM',
		'CG',
		'CD',
		'CK',
		'CR',
		'CI',
		'HR',
		'CU',
		'CW',
		'CY',
		'CZ',
		'DK',
		'DJ',
		'DM',
		'DO',
		'EC',
		'EG',
		'SV',
		'GQ',
		'ER',
		'EE',
		'ET',
		'FK',
		'FO',
		'FJ',
		'FI',
		'FR',
		'GF',
		'PF',
		'TF',
		'GA',
		'GM',
		'GE',
		'DE',
		'GH',
		'GI',
		'GR',
		'GL',
		'GD',
		'GP',
		'GU',
		'GT',
		'GG',
		'GN',
		'GW',
		'GY',
		'HT',
		'HM',
		'VA',
		'HN',
		'HK',
		'HU',
		'IS',
		'IN',
		'ID',
		'IR',
		'IQ',
		'IE',
		'IM',
		'IL',
		'IT',
		'JM',
		'JP',
		'JE',
		'JO',
		'KZ',
		'KE',
		'KI',
		'KP',
		'KR',
		'KW',
		'KG',
		'LA',
		'LV',
		'LB',
		'LS',
		'LR',
		'LY',
		'LI',
		'LT',
		'LU',
		'MO',
		'MK',
		'MG',
		'MW',
		'MY',
		'MV',
		'ML',
		'MT',
		'MH',
		'MQ',
		'MR',
		'MU',
		'YT',
		'MX',
		'FM',
		'MD',
		'MC',
		'MN',
		'ME',
		'MS',
		'MA',
		'MZ',
		'MM',
		'NA',
		'NR',
		'NP',
		'NL',
		'NC',
		'NZ',
		'NI',
		'NE',
		'NG',
		'NU',
		'NF',
		'MP',
		'NO',
		'OM',
		'PK',
		'PW',
		'PS',
		'PA',
		'PG',
		'PY',
		'PE',
		'PH',
		'PN',
		'PL',
		'PT',
		'PR',
		'QA',
		'RE',
		'RO',
		'RU',
		'RW',
		'BL',
		'SH',
		'KN',
		'LC',
		'MF',
		'PM',
		'VC',
		'WS',
		'SM',
		'ST',
		'SA',
		'SN',
		'RS',
		'SC',
		'SL',
		'SG',
		'SX',
		'SK',
		'SI',
		'SB',
		'SO',
		'ZA',
		'GS',
		'SS',
		'ES',
		'LK',
		'SD',
		'SR',
		'SJ',
		'SZ',
		'SE',
		'CH',
		'SY',
		'TW',
		'TJ',
		'TZ',
		'TH',
		'TL',
		'TG',
		'TK',
		'TO',
		'TT',
		'TN',
		'TR',
		'TM',
		'TC',
		'TV',
		'UG',
		'UA',
		'AE',
		'GB',
		'US',
		'UM',
		'UY',
		'UZ',
		'VU',
		'VE',
		'VN',
		'VG',
		'VI',
		'WF',
		'EH',
		'YE',
		'ZM',
		'ZW'
	);


	public $geoCode = true;
	public $showCoordinates = false;
	public $defaultCountry;
	public $fields = array(
		'name'       => false,
		'street'     => true,
		'street2'    => false,
		'postalCode' => true,
		'city'       => true,
		'state'      => false,
		'country'    => true,
	);


	public static function displayName(): string {
		return Craft::t( 'easyaddressfield', 'Easy Address Field' );
	}


	public function getContentColumnType(): string {
		return Schema::TYPE_STRING;
	}

	/**
	 * @return string
	 * @throws \yii\base\Exception
	 * @throws \Twig_Error_Loader
	 * @throws \RuntimeException
	 */
	public function getSettingsHtml(): string {
		// Render the settings template
		$data = new ISO3166();
		$data = $data->all();
		$countries = array();
		foreach($data as $country) {
			$countries[$country['alpha2']] = $country['name'];
		};

		return Craft::$app->getView()->renderTemplate(
			'easyaddressfield/_field/_settings',
			[
				'field'     => $this,
				'countries' => $countries
			]
		);
	}

	public function rules(): array {

		$addressRules =
			array(
				array(
					array(
						'geoCode'
					),
					'boolean'
				),
				array(
					array(
						'showCoordinates'
					),
					'boolean'
				),
				array(
					array(
						'defaultCountry'
					),
					'string'
				)
			);


		$rules = parent::rules();
		$rules = array_merge( $rules, $addressRules );

		return $rules;
	}

	/**
	 * @param mixed $value
	 * @param ElementInterface|null $element
	 *
	 * @return mixed|EasyAddressFieldModel
	 */
	public function normalizeValue( $value, ElementInterface $element = null ) {
		$settings = $this->getSettings();
		if ( is_string( $value ) ) {
			$value = json_decode( $value, true );
		}


		if ( is_array( $value ) && ! empty( array_filter( $value ) ) ) {
			return new EasyAddressFieldModel( $value );
		}

		return null;
	}

	public function serializeValue( $value, ElementInterface $element = null ) {
		$settings = $this->getSettings();
		if ( ! $value ) {
			return $value;
		}

		if ( $settings['geoCode'] and empty( $value['latitude'] ) and empty( $value['longitude'] ) ) {
			$service = new GeoLocationService();
			$value   = $service->geoLocate( $value );

		}

		return Db::prepareValueForDb( $value );
	}


	public function getInputHtml( $value, ElementInterface $element = null ): string {
		// Register our asset bundle
		Craft::$app->getView()->registerAssetBundle( EasyAddressFieldAsset::class );

		// Get our id and namespace
		$id           = Craft::$app->getView()->formatInputId( $this->handle );
		$namespacedId = Craft::$app->getView()->namespaceInputId( $id );

		$pluginSettings = Plugin::getInstance()->getSettings();
		$fieldSettings  = $this->getSettings();

		return $this->renderFormFields( $value );
	}

	protected function renderFormFields( EasyAddressFieldModel $value = null ) {
		// Get our id and namespace
		$id           = Craft::$app->getView()->formatInputId( $this->handle );
		$namespacedId = Craft::$app->getView()->namespaceInputId( $id );

		$fieldSettings  = $this->getSettings();
		$pluginSettings = Plugin::getInstance()->getSettings();

		$fieldLabels   = null;
		$addressFields = null;

		$iconUrl        = Craft::$app->assetManager->getPublishedUrl( '@studioespresso/easyaddressfield/assets', true, 'marker.svg' );
		$pluginSettings = Plugin::getInstance()->getSettings();

		Craft::$app->getView()->registerJsFile( 'https://maps.googleapis.com/maps/api/js?key=' . $pluginSettings->googleApiKey );

		return Craft::$app->getView()->renderTemplate(
			'easyaddressfield/_field/_input',
			[
				'name'           => $this->handle,
				'value'          => $value,
				'field'          => $this,
				'id'             => $id,
				'iconUrl'        => $iconUrl,
				'namespacedId'   => $namespacedId,
				'fieldSettings'  => $fieldSettings,
				'pluginSettings' => $pluginSettings,
			]
		);
	}

}