# Release Notes for Easy Address Field for Craft CMS

## 5.0.1 - 2024-08-07
### Fixed
- Fields now respect wether geocoding is enabled or not ([#38](https://github.com/studioespresso/craft-easyaddressfield/issues/38))

## 5.0.0 - 2024-02-10
### Added
- Finished Craft 5 release

## 4.0.8 - 2023-11-23
### Changed
- Fixed missing return statement in AddressFieldModel
- Moved getDirectionsUrl from the twig function to the field model and added deprecation notice


## 4.0.7 - 2023-03-22
### Fixed
- Improved the position of the map in the field and added an extra label [#36](https://github.com/studioespresso/craft3-easyaddressfield/issues/36)

## 4.0.6 - 2023-03-20
### Fixed
- Improved the position of the map in the field and added an extra label [#36](https://github.com/studioespresso/craft3-easyaddressfield/issues/36)


## 4.0.5 - 2023-03-07
### Fixed
- Fixed return type in GraphQL resolver

## 4.0.4 - 2022-11-8
### Fixed
- Fixed return type on feedme field class

## 4.0.3 - 2022-10-17
### Fixed
- Fixed an issue with the geolocation service
- Added parameters to isEmpty check


## 4.0.2 - 2022-06-02
### Fixed
- Fixed an issue with empty state where custom validators could not make the field required ([#33](https://github.com/studioespresso/craft3-easyaddressfield/issues/33))

## 4.0.1 - 2022-05-03
### Fixed
- Now compatible with giggsey/locale 2.0 or higher

## 4.0.0 - 2022-05-02
### Added
- Craft 4 🚀

## 3.1.0 - 2022-04-10
### Added
- Added support for PHP 8

## 3.0.2 - 2021-01-27
### Fixed
- Small fix to better catch multiple types of Nomanatim results ([#29](https://github.com/studioespresso/craft3-easyaddressfield/issues/29))


## 3.0.1 - 2020-11-29
### Fixed
- Fixed an error with geolocating when some sub-fields are disabled ([#26](https://github.com/studioespresso/craft3-easyaddressfield/issues/26)) 


## 3.0.0 - 2020-11-11
### Added
- The plugin now uses OpenStreetMaps for all geocoding requests so you no longer need a Google API key for those requests.

### Fixed
- Small bugfix in the field settings layout

## 2.2.5 - 2020-08-10
### Fixed
- Allow the address country to be empty

## 2.2.4 - 2020-04-11
### Fixed
- Fixed an issue with upgrading from StatikAddress when fields were inside a Matrix field

### Added
- Added placeholder functions for lat & long so upgrades don't crash but just show deprecation warnings


## 2.2.3 - 2020-01-21
### Fixed
- Countries that don't have a translation in the current locale will fall back to English now

## 2.2.2 - 2020-01-20
### Fixed
- Countries are now sorted aphabetically by name, not by country code

## 2.2.1 - 2020-01-15
### Added
- The countries list is now returns in the locale of the site or the cp.

## 2.2.0 - 2019-10-16
### Added
- Added ``formatted()`` function to the address model to make it easier to print the entrie address in 1 go.

### Fixed
- Fixed an error with GraphQL support when the field was added to more than 1 section.

## 2.1.0 - 2019-09-24
### Added
- Country names are now returns in the site's locale by default, instead of just in English.

### Fixed
- Fixed an issue where API keys from environment variable weren't parsed in static maps urls

## 2.0.0 - 2019-09-24
### Added
- Added support for Craft's native [GraphQL API](https://docs.craftcms.com/v3/graphql.html).

### Fixed
- Fixed an error when creating singles with the field through a migration/console request

## 1.4.0 - 2019-09-02
### Added
- Added a setting for a second Google API key, which is only used for non-geocoding request, to get around Google's API restrictions

## 1.3.0 - 2019-07-31
### Added
- Added support for [craftcms/feed-me](https://github.com/craftcms/feed-me/) so you can import addresses
- You can now use an environment variable for the Google API key

## 1.2.3 - 2019-05-09
### Added
- Added a migration for StatikAddress fields on install. Settings & content are migrated to EasyAddressField

### Fixed
- Only get do getCountryName() for fields which actually have a country

## 1.2.2 - 2019-03-20
### Improved
- Field data can now be added and view in element overviews
- Field data is now fully searchable, both on the CP and through twig

## 1.2.1 - 2019-03-07
### Fixed
- Address fields that are set as required now validated properly when they are empty

## 1.2.0 - 2019-02-21
### Added
- GraphQL support! The now also works in your GraphQL queries
- Added getCountryName() to get the full name of the country selected in the field

## 1.1.5 - 2019-02-06
### Fixed
- Don't redirect to settings when installing through command line
- Fixed a small possible error when installing the plugin on 3.1

## 1.1.4 - 2018-09-17
### Fixed
- Small fixes to field layout when different fields are selected
- Fixed an issue where the selected country value wasn't displayed after save

## 1.1.3 - 2018-04-21
### Changed
* Added better logging and error handling related to Google API responses.

## 1.1.2 - 2018-04-18
### Changed
* Fixed an issue with the country field when it was hidden in the field options

## 1.1.1 - 2018-03-06
### Changed
* Fixed an issue with the country field not being clickable in some field layouts

## 1.1.0 - 2018-02-20
### Added
* Scale option to static map variable

## 1.0.0 - 2018-01-21
* Initial release.