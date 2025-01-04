# Easy Address Field plugin for Craft CMS 3.x

![Easy Address Field](https://www.studioespresso.co/assets/Easy-Address-Field-Github-Banner.png)


The only address field you need

## Requirements

This plugin requires Craft CMS 3.0.0-RC1 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require studioespresso/craft-easyaddressfield

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for "Easy Address Field".

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
When `devMode` is enabled, any errors returned by Google's API will show an exception so you can clearly see what's going wrong. With `devMode` disabled, any errors will be logged to Craft's `web.log`.

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
````

## Upgrading from Craft 4 to Craft 5
### getDirectionsUrl()
If you're using the ``getDirectionsUrl()`` function on `craft.address`, you'll now need to call the function on the field itself instead of the on the plugin's Twig variable.

### getStaticMap() & getStaticMapRaw()
These functions have been removed from the plugin, you'll need to replace these yourself.

---

###### Brought to you by [Studio Espresso](https://studioespresso.co)
