# Changelog
All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this package adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [5.1.1] - 2019-07-10
### Changed
- Explicitly require authentication on API routes by default

## [5.1.0] - 2019-02-26
### Added
- Laravel 5.8 support
- `make:module:job` command

## [5.0.2] - 2019-02-11
### Fixed
- Fix undefined variable factory when loading factories

## [5.0.1] - 2018-12-09
### Fixed
- Cached config is now properly returned
- Properly reference default location when seeding modules

## [5.0.0] - 2018-11-30
### Added
- Module locations - define and configure as many locations to store your modules as desired
- Ability to configure manifest filename
- CHANGELOG document
- CONTRIBUTING document

### Changed
- Updated year in LICENSE document
- Customized directory mappings are now used in generated files

### Fixed
- Unit tests that were previously failing now pass with flying colors
- Custom service provider name is now taken into consideration when generating new modules

## [4.5.1] - 2018-10-31
### Removed
- Reverted factory autoload change

## [4.5.0] - 2018-10-24
### Added
- Factory classes will now autoload
- Added configuration option to customize module service provider namespace

## [4.4.0] - 2018-10-24
### Added
- Laravel 5.7 support

## [4.3.2] - 2018-05-29
### Added
- Laravel LTS support

## [4.3.1] - 2018-05-12
### Fixed
- `Modules->exists()` will not throw an exception when referencing an older module slug

## [4.3.0] - 2018-05-05
### Added
- Laravel 5.6 support

## [4.2.2] - 2017-09-18
### Added
- Laravel 5.5 support

## [4.2.1] - 2017-08-28
### Fixed
- Exception is no longer thrown if a module's description or name manifest info is missing

## [4.2.0] - 2017-05-26
### Added
- Laravel 5.4 support
- Manifest file validation
- `ModuleNotFound` exception

### Fixed
- Running `module:optimize` will no longer error if a module has been renamed/removed, and will properly update

## [4.1.6] - 2017-03-28
### Added
- Generator command for tests

## [4.1.5] - 2017-01-25
### Added
- Laravel 5.4 support

## [4.1.4] - 2017-01-09
### Fixed
- Removed instance of double slashes being add in generated controllers

## [4.1.3] - 2016-12-19
### Added
- Added `--step` option to `module:migrate` Artisan command

### Changed
- Refactored `module:migrate:reset` command

### Fixed
- Fixed incorrect reference in `module:seed` command
- Request validator now properly extends `FormRequest`
- Fixed `--quick` flag in `make:module` command

## [4.1.2] - 2016-12-11
### Fixed
- Initial StyleCI formatting

## [4.1.1] - 2016-11-07
### Fixed
- `module:migrate:rollback` command
    - Rolls back a batch created through `module:migrate`
    - Rolls back only the migration for the provided module

## [4.1.0] - 2016-11-06
### Added
- Allow the configuration of the module subdirectory structure

## [4.0.9] - 2016-11-02
### Changed
- `make:module` help text is now more descriptive

## [4.0.8] - 2016-11-01
### Added
- Wrap module helper methods in `function_exists` conditionals

## [4.0.7] - 2016-10-19
### Fixed
- Generate seeders in the right namespace

## [4.0.6] - 2016-10-18
### Added
- Pass all modules through `modules.optimized` event

## [4.0.5] - 2016-10-05
### Removed
- Migration service provider

## [4.0.4] - 2016-10-05
### Added
- Various improvements to the `module:migrate:rollback` command

## [4.0.3] - 2016-09-28
### Changed
- Using `session()->put()` instead of `session()->flash` in middleware

## [4.0.2] - 2016-09-04
### Added
- Unique ID to cached manifest per module

### Removed
- Database driver

## [4.0.1] - 2016-08-24
### Fixed
- Ensure cached module listing isn't deleted when running `module:optimize`
- Ensure all directories are kept and copied over when bootstraping a new module when using `make:module`

## [4.0.0] - 2016-08-23
### Added
- Laravel 5.3 support
