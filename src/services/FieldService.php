<?php

namespace studioespresso\easyaddressfield\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\ElementHelper;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\records\EasyAddressFieldRecord;

class FieldService extends Component
{

    /**
     * @param EasyAddressFieldField $field
     * @param ElementInterface $element
     *
     * @return bool
     */
    public function saveField(EasyAddressFieldField $field, ElementInterface $element)
    {

        $locale = $element->getSite()->language;
        $value = $element->getFieldValue($field->handle);

        $record = EasyAddressFieldRecord::findOne(
            [
                'owner' => $element->id,
                'site' => $element->siteId,
                'field' => $field->id,
            ]
        );

        if (!$record) {
            $record = new EasyAddressFieldRecord();
            $record->owner = $element->id;
            $record->site = $element->siteId;
            $record->field = $field->id;
        }
        if(!ElementHelper::isDraftOrRevision($element) && $field->geoCode) {
            $value = EasyAddressField::$plugin->geoLocation()->locate($value);
        }


        $record->name = $value->name;
        $record->street = $value->street;
        $record->street2 = $value->street2;
        $record->postalCode = $value->postalCode;
        $record->city = $value->city;
        $record->state = $value->state;
        $record->country = $value->country;
        $record->latitude = $value->latitude;
        $record->longitude = $value->longitude;


        $save = $record->save();
        if (!$save) {
            Craft::getLogger()->log($record->getErrors(), LOG_ERR, 'easy-address-field');
        }

        return $save;
    }

    /**
     * @param EasyAddressFieldField $field
     * @param ElementInterface $element
     * @param $value
     *
     * @return EasyAddressFieldModel
     */
    public function getField(EasyAddressFieldField $field, ElementInterface $element, $value)
    {
        $record = EasyAddressFieldRecord::findOne(
            [
                'owner' => $element->id,
                'site' => $element->siteId,
                'field' => $field->id,
            ]
        );
        if ($value) {
            $model = new EasyAddressFieldModel($value);
        } else if ($record) {
            $model = new EasyAddressFieldModel($record->getAttributes());
        } else {
            $model = new EasyAddressFieldModel();
        }

        return $model;
    }

}
