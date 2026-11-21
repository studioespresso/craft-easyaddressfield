# Upgrading to Easy Address Field 6 (Craft 6)

This guide lists what you need to update in your project and custom code when moving to Easy Address Field 6.

## Requirements

- Craft CMS 6
- PHP 8.5
- `craftcms/yii2-adapter` is not needed

Existing addresses are kept: the plugin uses the same `easyaddressfield` table, and existing fields keep working.

## Breaking changes

### Custom geocoders

Geocoders are registered with a Laravel event instead of a Yii event, and no longer extend `craft\base\Component`.

Before:

```php
use studioespresso\easyaddressfield\events\RegisterGeocoderEvent;
use studioespresso\easyaddressfield\services\GeoLocationService;
use yii\base\Event;

Event::on(GeoLocationService::class, GeoLocationService::EVENT_REGISTER_GEOCODERS, function (RegisterGeocoderEvent $event) {
    $event->geoCoders['your-service'] = YourGeoCoder::class;
});
```

After (in a service provider's `boot()`):

```php
use Illuminate\Support\Facades\Event;
use studioespresso\easyaddressfield\Events\RegisterGeocoderEvent;

Event::listen(function (RegisterGeocoderEvent $event) {
    $event->geoCoders['your-service'] = YourGeoCoder::class;
});
```

### Classes and namespaces

Classes moved to Laravel-style folders. The root namespace (`studioespresso\easyaddressfield`) is unchanged.

| Before | After |
| --- | --- |
| `studioespresso\easyaddressfield\events\RegisterGeocoderEvent` | `studioespresso\easyaddressfield\Events\RegisterGeocoderEvent` |
| `studioespresso\easyaddressfield\fields\EasyAddressFieldField` | `studioespresso\easyaddressfield\Fields\EasyAddressFieldField` |
| `studioespresso\easyaddressfield\models\EasyAddressFieldModel` | `studioespresso\easyaddressfield\Data\EasyAddressFieldModel` |
| `studioespresso\easyaddressfield\models\EasyAddressFieldSettingsModel` | `studioespresso\easyaddressfield\EasyAddressFieldSettingsModel` |
| `studioespresso\easyaddressfield\records\EasyAddressFieldRecord` | `studioespresso\easyaddressfield\Models\EasyAddressFieldRecord` (an Eloquent model) |
| `studioespresso\easyaddressfield\services\*` | `studioespresso\easyaddressfield\Services\*` |
| `studioespresso\easyaddressfield\services\geocoders\*` | `studioespresso\easyaddressfield\Services\GeoCoders\*` |

The old field class name, as stored in the database and project config, still resolves.

### Removed integrations

The Feed Me integration has been removed.

## Unchanged

- `craft.address.countries()` in Twig.
- Field values: `getCountryName()`, `getDirectionsUrl()` and the other field value methods.
- GraphQL: the field's type and its properties.
