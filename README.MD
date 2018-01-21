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

        composer require studioespresso/easyaddressfield

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for "Easy Address Field".

## Settings

### Google Maps API
To enable geolocating and custom marker placement, you need to provide a Google Maps API key.
1) Sign in to [Google's developers console](http://console.developers.google.com/)
2) Create a new project
3) Enable the `Google Maps Geocoding API` and the `Google Static Maps API`

### Other settings
- Default map styling (options are `silver`, `retro`, `dark`, `night` or `aubergine`)
- Default marker color
- Default marker icon

## Template variables

### Static map

The plugin makes it easy to render a static map image based on 1 or more address fields.

#### Available functions

`craft.easyaddressfield.getStaticMap`: returns an `<img src` element to the static map image (needs to be followed by the `|raw` filter to show the image)

`craft.easyaddressfield.getStaticMapRaw`: returns a link to the static map image, which you can use how you like yourself.

#### Usage

`{{ craft.easyaddressfield.getStaticMap(entry.addressFieldHandle) }}`

To include multiple address fields in the static map, add them as an array:

`{{ craft.easyaddressfield.getStaticMap([entry.addressFieldHandle, entry.addressFieldHandle2 ]) }}`

#### Available settings

- **data** (the field or fields to use)
- **zoom** (the zoom level, integer between 1 & 17)
- **size** (widthxheight, defaults to `640x640` which is also the maximum size)
- **style** (name of the style the map should be using, falls back to the style set in settings)
- **color** (hex value to be used as color for the marker(s), fallback to the color set in settings)
- **icon** (url that should be used as icon for the marker(s))

###### Brought to you by [Studio Espresso](https://studioespresso.co)
