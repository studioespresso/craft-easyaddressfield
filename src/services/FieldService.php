<?php

namespace studioespresso\easyaddressfield\services;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Element\ElementHelper;
use studioespresso\easyaddressfield\EasyAddressField;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\models\EasyAddressFieldModel;
use studioespresso\easyaddressfield\records\EasyAddressFieldRecord;

class FieldService
{
    public function saveField(EasyAddressFieldField $field, ElementInterface $element): void
    {
        $value = $element->getFieldValue($field->handle);
        if (! $value instanceof EasyAddressFieldModel) {
            return;
        }

        if (! ElementHelper::isDraftOrRevision($element) && $field->geoCode) {
            $value = EasyAddressField::getInstance()->geoLocation->locate($value);
        }

        EasyAddressFieldRecord::query()->updateOrCreate(
            ['owner' => $element->id, 'site' => $element->siteId, 'field' => $field->id],
            [
                'name' => $value->name,
                'street' => $value->street,
                'street2' => $value->street2,
                'postalCode' => $value->postalCode,
                'city' => $value->city,
                'state' => $value->state,
                'country' => $value->country,
                'latitude' => $value->latitude,
                'longitude' => $value->longitude,
            ],
        );
    }

    public function getField(EasyAddressFieldField $field, ?ElementInterface $element, mixed $value): EasyAddressFieldModel
    {
        if ($value) {
            return new EasyAddressFieldModel($value);
        }

        if (! $element?->id) {
            return new EasyAddressFieldModel;
        }

        $record = EasyAddressFieldRecord::query()
            ->where(['owner' => $element->id, 'site' => $element->siteId, 'field' => $field->id])
            ->first();

        return new EasyAddressFieldModel($record?->getAttributes() ?? []);
    }
}
