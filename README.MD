# Easy Address Field plugin for Craft CMS 3.x

![Easy Address Field](/resources/banner.png?raw=true)


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
