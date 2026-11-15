<?php

namespace studioespresso\easyaddressfield\form;

use CraftCms\Cms\Support\Html;
use CraftCms\Cms\Ui\ControlPayload;
use CraftCms\Cms\Ui\Controls\Control;
use CraftCms\Cms\Ui\UiHtmlRenderer;

use function CraftCms\Cms\t;

/**
 * The Easy Address Field input: the address lines, a country select, coordinates and a map to drag a pin on.
 * Its value is the flat address array (`name`, `street`, … `latitude`, `longitude`).
 *
 * The Vue component is registered by `resources/dist/js/EasyAddressField.js`.
 */
class AddressControl extends Control
{
    /** @var list<array{key: string, label: string, width: int}> */
    private array $inputs = [];

    /** @var list<array{label: string, value: string}>|null */
    private ?array $countries = null;

    private bool $showCoordinates = false;

    public static function renderHtml(ControlPayload $control, mixed $value, array $attributes, UiHtmlRenderer $renderer): string
    {
        $value = is_array($value) ? $value : [];
        $name = fn (string $key) => $attributes['name'] === null ? null : "{$attributes['name']}[$key]";
        $input = fn (string $key, string $label, string $type = 'text') => Html::tag('input', '', [
            'type' => $type,
            'class' => 'text fullwidth',
            'name' => $name($key),
            'value' => $value[$key] ?? null,
            'placeholder' => $label,
            'aria-label' => $label,
            'disabled' => $attributes['disabled'],
            'readonly' => $attributes['readonly'],
        ]);

        $html = '';
        foreach ($control->props['inputs'] as $field) {
            $html .= $input($field['key'], $field['label']);
        }

        if ($control->props['countries'] === null) {
            $html .= $input('country', '', 'hidden');
        } else {
            $options = array_map(fn (array $option) => Html::tag('option', e($option['label']), [
                'value' => $option['value'],
                'selected' => ($value['country'] ?? null) === $option['value'],
            ]), $control->props['countries']);
            $html .= Html::tag('select', implode('', $options), ['name' => $name('country'), 'disabled' => $attributes['disabled'] || $attributes['readonly']]);
        }

        // No map without the CP's Vue renderer; coordinates still come from geocoding
        foreach (['latitude', 'longitude'] as $key) {
            $html .= $input($key, $control->props['labels'][$key], $control->props['showCoordinates'] ? 'text' : 'hidden');
        }

        return Html::tag('div', $html, ['id' => $attributes['id'], 'class' => 'easyaddressfield']);
    }

    public function component(): string
    {
        return 'easy-address-field:address';
    }

    /** @param list<array{key: string, label: string, width: int}> $inputs Text inputs in order, `width` is a percentage */
    public function inputs(array $inputs): static
    {
        $this->inputs = $inputs;

        return $this;
    }

    /** @param list<array{label: string, value: string}>|null $countries Country options, or null to post the value in a hidden input */
    public function countries(?array $countries): static
    {
        $this->countries = $countries;

        return $this;
    }

    public function showCoordinates(bool $showCoordinates = true): static
    {
        $this->showCoordinates = $showCoordinates;

        return $this;
    }

    public function emptyValue(): mixed
    {
        return [];
    }

    public function props(mixed $value = null): array
    {
        return [
            'inputs' => $this->inputs,
            'countries' => $this->countries,
            'showCoordinates' => $this->showCoordinates,
            'labels' => [
                'country' => t('Country', category: 'easy-address-field'),
                'latitude' => t('Latitude', category: 'easy-address-field'),
                'longitude' => t('Longitude', category: 'easy-address-field'),
                'pick' => t('Pick an address on the map', category: 'easy-address-field'),
                'cancel' => t('Cancel'),
                'done' => t('Done'),
            ],
        ];
    }
}
