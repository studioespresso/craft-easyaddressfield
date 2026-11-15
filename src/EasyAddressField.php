<?php

/**
 * @copyright Copyright (c) 2017 Studio Espresso
 */

namespace studioespresso\easyaddressfield;

use CraftCms\Cms\Address\Elements\Address;
use CraftCms\Cms\Cp\SelectOptions;
use CraftCms\Cms\Element\ElementHelper;
use CraftCms\Cms\Element\Events\ElementSaving;
use CraftCms\Cms\Plugin\Plugin;
use CraftCms\Cms\Plugin\PluginSettings;
use CraftCms\Cms\Twig\Variables\CraftVariable;
use CraftCms\Cms\Ui\Controls\Choice;
use CraftCms\Cms\Ui\Controls\Lightswitch;
use CraftCms\Cms\Ui\Controls\Text;
use CraftCms\Cms\Ui\Nodes\Field;
use CraftCms\Cms\Ui\Ui;
use CraftCms\Cms\Ui\UiContext;
use CraftCms\Cms\Ui\UiControlTypes;
use Illuminate\Support\Facades\Event;
use studioespresso\easyaddressfield\fields\EasyAddressFieldField;
use studioespresso\easyaddressfield\form\AddressControl;
use studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel;
use studioespresso\easyaddressfield\services\CountriesService;
use studioespresso\easyaddressfield\services\FieldService;
use studioespresso\easyaddressfield\services\GeoLocationService;

use function CraftCms\Cms\t;

/**
 * Plugin represents the Easy Address Field plugin.
 *
 * @author Studio Espresso <support@studioespresso.co>
 *
 * @since  1.0
 *
 * @method EasyAddressFieldSettingsModel getSettings()
 */
class EasyAddressField extends Plugin
{
    public bool $hasCpSettings = true;

    protected array $fieldTypes = [EasyAddressFieldField::class];

    protected array $styles = [
        __DIR__.'/../resources/dist/css/leaflet.css' => 'css/leaflet.css',
        __DIR__.'/../resources/dist/css/EasyAddressField.css' => 'css/EasyAddressField.css',
    ];

    protected array $scripts = [
        __DIR__.'/../resources/dist/js/leaflet.js' => 'js/leaflet.js',
        __DIR__.'/../resources/dist/js/EasyAddressField.js' => 'js/EasyAddressField.js',
    ];

    protected array $publishables = [
        __DIR__.'/../resources/dist/css/images' => 'css/images',
    ];

    public FieldService $field { get => $this->field ??= new FieldService; }

    public GeoLocationService $geoLocation { get => $this->geoLocation ??= new GeoLocationService; }

    public CountriesService $countries { get => $this->countries ??= new CountriesService; }

    public function boot(UiControlTypes $controlTypes): void
    {
        $controlTypes->register(AddressControl::class);

        // Keeps `craft.address.countries()` working in templates
        CraftVariable::macro('address', fn () => new class
        {
            /** @return array<string, string> */
            public function countries(): array
            {
                return EasyAddressField::getInstance()->countries->getCountriesAsArray();
            }
        });

        Event::listen(ElementSaving::class, function (ElementSaving $event) {
            if ($event->element instanceof Address
                && $this->getSettings()->enableGeoCodingForCraftElements
                && ! ElementHelper::isDraftOrRevision($event->element)) {
                $this->geoLocation->locateElement($event->element);
            }
        });
    }

    public function getField(): FieldService
    {
        return $this->field;
    }

    public function geoLocation(): GeoLocationService
    {
        return $this->geoLocation;
    }

    protected static function createSettings(): ?PluginSettings
    {
        return new EasyAddressFieldSettingsModel;
    }

    public function settingsUi(UiContext $context = new UiContext): ?Ui
    {
        $geoCoders = $this->geoLocation->geoCoders
            ->map(fn ($geoCoder, string $handle) => ['label' => (string) $geoCoder->name, 'value' => $handle])
            ->values()
            ->all();

        return Ui::make([
            Field::make(t('Geocoding service', category: 'easy-address-field'), Choice::make('geoCodingService')->options($geoCoders))
                ->instructions(t('The geocoding service used, defaults to Nomanatim', category: 'easy-address-field')),
            Field::make(t('Google API Key for geocoding requests', category: 'easy-address-field'), Text::make('googleApiKey')->textExpanderTriggers(SelectOptions::getEnvTextExpanderTriggers()))
                ->instructions(t('Only needed if you are using Google as the geocoding service. Environment variables like `$GOOGLE_API_KEY` are supported.', category: 'easy-address-field')),
            Field::make(t('Enable geocoding for Craft Address fields/elements', category: 'easy-address-field'), Lightswitch::make('enableGeoCodingForCraftElements')),
        ]);
    }
}
