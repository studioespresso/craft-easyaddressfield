<?php

namespace studioespresso\easyaddressfield\Services;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Element\ElementHelper;
use Illuminate\Container\Attributes\Singleton;
use studioespresso\easyaddressfield\Data\EasyAddressFieldModel;
use studioespresso\easyaddressfield\Fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\Models\EasyAddressFieldRecord;

#[Singleton]
class FieldService
{
    public function __construct(private readonly GeoLocationService $geoLocation) {}

    public function saveField(EasyAddressFieldField $field, ElementInterface $element): void
    {
        $value = $element->getFieldValue($field->handle);
        if (! $value instanceof EasyAddressFieldModel) {
            return;
        }

        if (! ElementHelper::isDraftOrRevision($element) && $field->geoCode) {
            $value = $this->geoLocation->locate($value);
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
