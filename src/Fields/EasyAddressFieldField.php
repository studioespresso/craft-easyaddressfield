<?php

namespace studioespresso\easyaddressfield\Fields;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\PreviewableFieldInterface;
use CraftCms\Cms\Field\Field;
use CraftCms\Cms\Field\FieldContext;
use CraftCms\Cms\Ui\Contracts\Control;
use CraftCms\Cms\Ui\Controls\Choice;
use CraftCms\Cms\Ui\Controls\Lightswitch;
use CraftCms\Cms\Ui\Nodes\Field as FormField;
use CraftCms\Cms\Ui\Ui;
use CraftCms\Cms\Ui\UiContext;
use GraphQL\Type\Definition\Type;
use studioespresso\easyaddressfield\Data\EasyAddressFieldModel;
use studioespresso\easyaddressfield\Gql\EasyAddressFieldTypeGenerator;
use studioespresso\easyaddressfield\Services\CountriesService;
use studioespresso\easyaddressfield\Services\FieldService;
use studioespresso\easyaddressfield\Ui\AddressControl;

use function CraftCms\Cms\t;

class EasyAddressFieldField extends Field implements PreviewableFieldInterface
{
    public bool $geoCode = true;

    public bool $showCoordinates = false;

    public ?string $defaultCountry = null;

    /**
     * Which optional inputs are shown. Older field settings store the handle as the value (`['name' => 'name']`), so any truthy value counts.
     *
     * @var array<string, mixed>
     */
    public array $enabledFields = [
        'name' => false,
        'street' => true,
        'street2' => false,
        'postalCode' => true,
        'city' => true,
        'state' => false,
        'country' => true,
    ];

    public static function displayName(): string
    {
        return t('Easy Address Field', category: 'easy-address-field');
    }

    public static function icon(): string
    {
        return 'location-dot';
    }

    public static function phpType(): string
    {
        return EasyAddressFieldModel::class;
    }

    /**
     * Values live in the plugin's own table
     */
    public static function dbType(): null
    {
        return null;
    }

    public function getRules(): array
    {
        return array_merge(parent::getRules(), [
            'geoCode' => ['boolean'],
            'showCoordinates' => ['boolean'],
            'defaultCountry' => ['nullable', 'string'],
        ]);
    }

    public function settingsUi(UiContext $context = new UiContext): Ui
    {
        $field = fn (string $key, string $label) => FormField::make(t($label, category: 'easy-address-field'), Lightswitch::make("enabledFields.$key")->value($this->isEnabled($key)));

        return Ui::make([
            $field('name', 'Name'),
            $field('street2', 'Street 2'),
            $field('state', 'State'),
            $field('country', 'Country'),
            FormField::make(t('Default country', category: 'easy-address-field'), Choice::make('defaultCountry')
                ->options($this->countryOptions())
                ->value($this->defaultCountry ?: 'US')),
            FormField::make(t('Get coordinates for addresses', category: 'easy-address-field'), Lightswitch::make('geoCode')->value($this->geoCode)),
            FormField::make(t('Show coordinates?', category: 'easy-address-field'), Lightswitch::make('showCoordinates')->value($this->showCoordinates)),
        ]);
    }

    public function getContentGqlType(): Type|array
    {
        return [
            'name' => $this->handle,
            'description' => 'Easy Address Field field',
            'type' => EasyAddressFieldTypeGenerator::generateType($this),
        ];
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element): EasyAddressFieldModel
    {
        if ($value instanceof EasyAddressFieldModel) {
            return $value;
        }

        return app(FieldService::class)->getField($this, $element, $value);
    }

    /**
     * Geocoding happens when the value is saved, see FieldService::saveField()
     */
    public function serializeValue(mixed $value, ?ElementInterface $element): ?array
    {
        return $value instanceof EasyAddressFieldModel ? $value->toArray() : null;
    }

    public function isValueEmpty(mixed $value, ElementInterface $element): bool
    {
        return ! $value instanceof EasyAddressFieldModel || $value->isEmpty();
    }

    public function getPreviewHtml(mixed $value, ElementInterface $element): string
    {
        return $value instanceof EasyAddressFieldModel ? e($value->modelToString(', ')) : '';
    }

    protected function searchKeywords(mixed $value, ElementInterface $element): string
    {
        return $value instanceof EasyAddressFieldModel ? $value->modelToString(' ') : '';
    }

    public function uiControl(FieldContext $context): Control
    {
        /** @var EasyAddressFieldModel $value */
        $value = $context->value;
        $input = fn (string $key, string $label, int $width = 100) => ['key' => $key, 'label' => t($label, category: 'easy-address-field'), 'width' => $width];

        return AddressControl::make($context->path)
            ->inputs(array_values(array_filter([
                $this->isEnabled('name') ? $input('name', 'Name') : null,
                $input('street', 'Street'),
                $this->isEnabled('street2') ? $input('street2', 'Street 2') : null,
                $input('postalCode', 'Postal code', 25),
                $input('city', 'City', $this->isEnabled('state') ? 50 : 75),
                $this->isEnabled('state') ? $input('state', 'State', 25) : null,
            ])))
            ->countries($this->isEnabled('country') ? $this->countryOptions() : null)
            ->showCoordinates($this->showCoordinates)
            ->value([...$value->toArray(), 'country' => $value->country ?: $this->defaultCountry]);
    }

    public function afterElementSave(ElementInterface $element, bool $isNew): void
    {
        app(FieldService::class)->saveField($this, $element);
        parent::afterElementSave($element, $isNew);
    }

    private function isEnabled(string $key): bool
    {
        return ! empty($this->enabledFields[$key]);
    }

    /** @return list<array{label: string, value: string}> */
    private function countryOptions(): array
    {
        $options = [];
        foreach (app(CountriesService::class)->getCountriesAsArray() as $code => $name) {
            $options[] = ['label' => $name, 'value' => $code];
        }

        return $options;
    }
}
