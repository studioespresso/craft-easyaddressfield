<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\Plugin;
use studioespresso\easyaddressfield\records\EasyAddressFieldRecord;
use yii\web\View;

class FieldService extends Component {

	public function saveField( $field, $element ) {

		$locale = $element->getSite()->language;
		$value  = $element->getFieldValue( $field->handle );

		$record = EasyAddressFieldRecord::findOne(
			[
				'owner' => $element->id,
				'site'  => $element->siteId,
				'field' => $field->id,
			]
		);

		if ( ! $record ) {
			$record        = new EasyAddressFieldRecord();
			$record->owner = $element->id;
			$record->site  = $element->siteId;
			$record->field = $field->id;
		}

		$record->name       = $value->name;
		$record->street     = $value->street;
		$record->street2    = $value->street2;
		$record->postalCode = $value->postalCode;
		$record->city       = $value->city;
		$record->country    = $value->country;


		$save = $record->save();

		return $save;
	}
	// MapField $field, ElementInterface $owner, $value): Map
	public function getField(EasyAddressFieldField $field, ElementInterface $element, $value) {
		$record = EasyAddressFieldRecord::findOne(
			[
				'owner' => $element->id,
				'site'  => $element->siteId,
				'field' => $field->id,
			]
		);
		if (Craft::$app->request->getIsPost() && $value) {
			$field = new EasyAddressFieldModel($value);
		} else if ($record) {
			$field = new EasyAddressFieldModel($record->getAttributes());
		} else {
			$field = new EasyAddressFieldModel();
		}
		return $field;
	}

}
