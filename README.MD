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

To enable geolocating and custom marker placement, you need to provide a Google Maps API key.

## Template variables

### Static map

The plugin makes it easy to render a static map image based on 1 or more address fields.

#### Available functions

`craft.easyaddressfield.getStaticMap`: returns an `<img src` element to the static map image (needs to be followed by the `|raw` filter to show the image)

`craft.easyaddressfield.getStaticMapRaw`: returns a link to the static map image, which you can use how you like yourself.

#### Basic static maps

`{{ craft.easyaddressfield.getStaticMap(entry.addressFieldHandle) }}`

To include multiple address fields in the static map, add them as an array:

`{{ craft.easyaddressfield.getStaticMap([entry.addressFieldHandle, entry.addressFieldHandle2 ]) }}`

###### Brought to you by [Studio Espresso](https://studioespresso.co)
