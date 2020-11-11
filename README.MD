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

## Settings

### Geocoding

The plugin uses [OpenStreetMaps Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim) as its geocoding service.

### Google Maps API
To enable stqtic map rendering and custom marker placement, you need to provide a Google Maps API key.
1) Sign in to [Google's developers console](http://console.developers.google.com/)
2) Create a new project
3) Enable the `Google Static Maps API`

### Other settings
- Default map styling (options are `silver`, `retro`, `dark`, `night` or `aubergine`)
- Default marker color
- Default marker icon

## Template variables

### Printing address values
You can access the values from your address field in Twig through the following variables:
````
field.name
field.street
field.street2
field.postalCode
field.state
field.country // returns the country code
field.getCountryName(craft.app.locale) // returns the coutry's full name, in the country's locale
field.latitude
field.longitude
````

### Static map

The plugin makes it easy to render a static map image based on 1 or more address fields.

#### Available functions

`craft.address.getStaticMap`: returns an `<img src` element of the static map image (needs to be followed by the `|raw` filter to show the image)

`craft.address.getStaticMapRaw`: returns a link to the static map image, which you can use how you like yourself.

`craft.address.getDirectionsUrl`: get a directions link to the given address

#### Usage

`{{ craft.address.getStaticMap(entry.addressFieldHandle) }}`

To include multiple address fields in the static map, add them as an array:

`{{ craft.address.getStaticMap([entry.addressFieldHandle, entry.addressFieldHandle2 ]) }}`

#### Available settings

- **data** (the field or fields to use)
- **zoom** (the zoom level, integer between `0` & `21`)
- **size** (widthxheight, defaults to `640x640` which is also the maximum size)
- **style** (name of the style the map should be using, falls back to the style set in settings)
- **color** (hex value to be used as color for the marker(s), fallback to the color set in settings)
- **icon** (url that should be used as icon for the marker(s))
- **scale** (the scale level of the image, integer, `1` or `2`)


#### Examples:

1) Static map with default options:
`{{ craft.address.getStaticMap(entry.addressField)|raw }}`

![Easy Address Field](/resources/default_static.png?raw=true)

2) Static map with custom settings:
`{{ craft.address.getStaticMap(entry.addressField2, 16, '500x350', 'night', '#00ff00')|raw }}`

![Easy Address Field](/resources/custom_static.png?raw=true)



###### Brought to you by [Studio Espresso](https://studioespresso.co)
