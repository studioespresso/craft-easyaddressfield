# Release Notes for Easy Address Field for Craft CMS

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