# Easy Address Field plugin for Craft CMS

![Easy Address Field](https://www.studioespresso.co/assets/Easy-Address-Field-Github-Banner.png)


The only address field you need

## Requirements

This plugin requires Craft CMS 6.0.0 or later and PHP 8.5.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require studioespresso/craft-easyaddressfield

3. Install the plugin from Settings → Plugins in the Control Panel, or run `php craft plugin/install easy-address-field`.

## Settings

You can choose which geocoding API the plugin will use when you have geocoding enabled for your fields.
Options are:
- OpenStreetMap's Nominatim (default, no configuration, but very strict rate limits)
- Google Maps (requires an API key, see below)

### Google Maps API
To enable geolocating and custom marker placement, you need to provide a Google Maps API key.
1) Sign in to [Google's developers console](http://console.developers.google.com/)
2) Create a new project
3) Enable the `Google Maps Geocoding API` and the `Google Static Maps API`

**🚨 Important 🚨:**

Google's Geocoding API does not support HTTP referrer restrictions. Make sure the API key you are using does not have these set.

#### API Errors
When `APP_DEBUG` is enabled, any errors returned by Google's API will show an exception so you can clearly see what's going wrong. Otherwise, errors are written to the Laravel log.


### GeoCoding for Craft Address elements
Craft has a built-in element for Addresses, but no way to get coordinates for addresses out of the box.
When you install Easy Address Field, you can enable GeoCoding for Craft Address elements.  This will add a `latitude` and `longitude` values to each Address element.
You can enable this feature in the plugin settings.

## Template variables

### Printing address values
You can access the values from your address field in Twig through the following variables:
````twig
field.name
field.street
field.street2
field.postalCode
field.state
field.country // returns the country code
field.getCountryName(craft.app.locale) // returns the coutry's full name, in the country's locale
field.latitude
field.longitude
field.getDirectionsUrl() // get a directions link to the given address
field.formatted(true) // the address as HTML lines, pass true to include the country
````

### Countries
`craft.address.countries()` returns all countries, keyed by their alpha-2 code.

## GraphQL
Each field gets a `{fieldHandle}_EasyAddressField` type with `name`, `street`, `street2`, `postalCode`, `city`, `state`, `country`, `latitude` and `longitude`.


## Custom GeoCoding services
Out of the box, the plugin comes with support for geocoding with OpenStreetMap's Nominatim service and Google.
If you'd like to use a different service, create a class that extends `studioespresso\easyaddressfield\Services\GeoCoders\BaseGeoCoder`.

Once you created your geocoder, register it in a service provider:

````php
use Illuminate\Support\Facades\Event;
use studioespresso\easyaddressfield\Events\RegisterGeocoderEvent;

Event::listen(function (RegisterGeocoderEvent $event) {
    $event->geoCoders['your-service'] = YourGeoCoder::class;
});
````

## Upgrading from Craft 5 to Craft 6
See [UPGRADE.md](UPGRADE.md).

## Upgrading from Craft 4 to Craft 5
### getDirectionsUrl()
If you're using the ``getDirectionsUrl()`` function on `craft.address`, you'll now need to call the function on the field itself instead of the on the plugin's Twig variable.

### getStaticMap() & getStaticMapRaw()
These functions have been removed from the plugin, you'll need to replace these yourself.

---

###### Brought to you by [Studio Espresso](https://studioespresso.co)
