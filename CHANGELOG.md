# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.10.0...main)

## [v2.10.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.9.1...v2.10.0) - 2022-08-08

### Added

- Config to change header name to detect locale from api requests ([b005313e](https://github.com/hexidedigital/hexide-admin-panel/commit/b005313e8ff68e50b2cfc95f97b6620d751aa635))
- Stay on the same tab after successfully saving the form ([6c9990c6](https://github.com/hexidedigital/hexide-admin-panel/commit/6c9990c62b6267bbd3fdb0cad8bcf8818e60b8e6))

### Fixed

- `DB::rollback` never called on exceptions ([1a041ab8](https://github.com/hexidedigital/hexide-admin-panel/commit/1a041ab8c8fc0fe3bfcfecc578a88f84d2b06596))
- Displaying errors in console on login page ([2ca82f7e](https://github.com/hexidedigital/hexide-admin-panel/commit/2ca82f7ef483824c11ed840ff67ef3cbffaf93c3))

### Updated

- Command for generation .env file ([58c912d0](https://github.com/hexidedigital/hexide-admin-panel/commit/58c912d04e6aa0d3b7f635ca0d6ea7960c0f3fbb))
- Changed columns api for **livewire table**, added new types ([62a9e533](https://github.com/hexidedigital/hexide-admin-panel/commit/62a9e53348a0a31e861efa3f08193e64fc3c274c))
- Updated markup for tabs component, tab links and added options to enable transition for some use-cases ([b97ef32a](https://github.com/hexidedigital/hexide-admin-panel/commit/b97ef32a4c9e543de3951c46e5abb28e798c4b15), [30323330](https://github.com/hexidedigital/hexide-admin-panel/commit/3032333028fd0266d85f155710074027d390e465))
- Updated resolving translation for breadcrumbs ([b434ac21](https://github.com/hexidedigital/hexide-admin-panel/commit/b434ac213340cc86ae17de2f3ebf89840e4fca49))
- Replaced default status code values onto symfony response constants in `ApiController` ([7c6d0bb2](https://github.com/hexidedigital/hexide-admin-panel/commit/7c6d0bb2516bb16bde381175cce6c2ec229ea094))
- Updated config options for page preloader ([d2aefe4a](https://github.com/hexidedigital/hexide-admin-panel/commit/d2aefe4ab39a28e100793ca7998de1102f6235b8))

### Deprecated

- `hd-admin:utils:clean-seed-files` command ([ad7e0786](https://github.com/hexidedigital/hexide-admin-panel/commit/ad7e0786bde844d33e242134dc6e09a9988ef1b9))

### Removed

- `locales_map` config option ([c6de5dad](https://github.com/hexidedigital/hexide-admin-panel/commit/c6de5dada3fd557fe22a7fb06a72f9b584213f10))
- `auth` lang files from source folder ([aa0b5872](https://github.com/hexidedigital/hexide-admin-panel/commit/aa0b5872a448fa657246b8fd21f8c994af76753d))

## [v2.9.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.9.0...v2.9.1) - 2022-07-10

### Added

- Method to call only form routes to prepare view data ([44b5f688](https://github.com/hexidedigital/hexide-admin-panel/commit/44b5f6888e156a7923394e9687033762a2f7c116))

### Fixed

- Fixed styles for from buttons lines
- Fixed incorrect generating column and table names for migration
- Fixed freezing active tab state for new duplicated row

### Changed

- Added class for menu icon
- Removed generating migrations with touching roles and added example file for seeding roles
- Cosmetic changes in blade files for tabs, admin configurations
- Updated supported `gitlab-deploy` version to **0.4**

## [v2.9.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.8.2...v2.9.0) - 2022-06-11

### Added

- Searching by key or value into translations table

### Fixed

- Fix: not change status for configuration in form
- Fixed storing image type configuration from list page
- Fixed syntax error for `declension_key` function

### Changed

- Changed namespace for create env command to `hd-admin:env-create`
- Refactored translations table module
- Updated `ListUpdateRequest` and added assertion for type availability in some functions
- Displaying additional debug information about translation in table

## [v2.8.2](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.8.1...v2.8.2) - 2022-05-25

### Fixed

- Fixed saving configurations from list page
- Fixed generating incorrect table names for migration

### Changed

- Changed properties visibility for `BackendController` from **private** to **protected**

## [v2.8.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.8.0...v2.8.1) - 2022-05-15

### Fixed

- Fixed table toggle ajax inputs that broke after the table reload

### Updated

- Changed table reload time to 20 seconds

## [v2.8.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.7.4...v2.8.0) - 2022-05-15

### Removed

- Moved deploy-prepare command to separated package

## [v2.7.4](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.7.3...v2.7.4) - 2022-05-15

### Fixed

- Fixed file templates for generating routes
- Fixed protection check for create and update methods in controller

## [v2.7.3](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.7.1...v2.7.3) - 2022-04-22

### Changed

- Code style: added return types for controller methods

## [v2.7.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.7.0...v2.7.1) - 2022-04-21

### Fixed

- Fixes for registering views and components

### Changed

- Updated `ide.json` file views

## [v2.7.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.5...v2.7.0) - 2022-04-20

> Many changes, see comparison - [v2.6.5...v2.7.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.5...v2.7.0)

### Added

- Trait for models with `priority` column
- Trait for publishable models
- Trait for models with price column
- Added hooks in `BackendController` for controller initialization and for call actions
- New columns for livewire tables - image, title, position
- Added `selfDescribed` option for backend controllers
- Added way to change input names for **image-preview** input component

### Fixed

- Fix `duplication_row` - broken tabs for old structured templates
- Generating input names for duplicated rows
- Passing **url_parameters** for action buttons in table

### Changed

- Changed `position` columns - **unsigned** and **nullable** by default
- Updated **tab-component** for working with more than one tab group on page
- Updated resolving class names in `BackendController`
- Added to files strict type
- Changed **pipe** separated validation rules to **array list** style
- Changed method to create class instances with `App::get()` method
- Changed namespace registering for blade components
- Updated `PrepareDeployCommand`
- Enabled by default creating form with enabled file sending
- Refactored translations controller
- Updated `FactoryWithTranslations`
- Refactored working with model - getting attributes, keys and other with function call

## [v2.6.5](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.3...v2.6.5) - 2022-04-08

### Added

- `ApiController` added method for http:204 (No content) status

### Changed

- `ApiController` structure for token response
- Processing and rendering ajax toggle button with additional attributes

## [v2.6.3](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.2...v2.6.3) - 2022-02-23

### Fixed

- Fixed storing images in `BackendController`

## [v2.6.2](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.1...v2.6.2) - 2022-02-23

### Added

- Added `ide.json` file for **Laravel idea** plugin

### Fixed

- Fixed `RoleRequest` file

## [v2.6.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.6.0...v2.6.1) - 2022-02-23

### Changed

- Simply added version to trigger repository indexing in composer

## [v2.6.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.5.2...v2.6.0) - 2022-02-14

### Added

- Created new commands for some pieces from full module create command
- Registering admin routes from package service provider
- Added helper trait for **FormRequest** to retrieve model id from request/route and default auth protection

### Fixed

- Fixes in file templates

### Changed

- Updated files - added `declare(strict_types=1);`
- Removed secondary service providers
- Changed values in config namespace

## [v2.5.2](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.5.1...v2.5.2) - 2022-02-07

### Changed

- Changed method to resolving locale for admin configurations
- Updated method to clear and store configs into cache

## [v2.5.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.5.0...v2.5.1) - 2022-01-31

### Fixed

- Removed catching validation errors in backend controller

### Changed

- Updated files formatting and small fixes

## [v2.5.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.4.1...v2.5.0) - 2022-01-27

### Added

- Added profile route and service for User

### Changed

- Updated method to get instance of `Configuration` class from application container
- Updated **callAction** method
- Changed action protection methods
- Changed semantic and style for **next action** buttons on form

## [v2.4.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.4.0...v2.4.1) - 2022-01-26

### Added

- Added config for admin rotues

### Changed

- Changed behavior for **notify** function
- Changed file template for route and menu item

## [v2.4.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.3.0...v2.4.0) - 2022-01-26

### Added

- Added helper function `module_name_from_model` to other files

## [v2.3.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.2.3...v2.3.0) - 2022-01-26

### Added

- Added filters for translations table
- Added new additional info for translations table
- Added helper function `module_name_from_model` for getting module name from model object

### Changed

- Added column for `RoleTable` to show **access_status**
- Updated views for **roles** to manage **permission**

## [v2.2.3](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.2.2...v2.2.3) - 2022-01-25

### Fixed

- Fixed registering translations table

### Changed

- Optimized loading roles info filters for User table

## [v2.2.2](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.2.1...v2.2.2) - 2022-01-25

### Added

- Added reporting exceptions in backend controller

## [v2.2.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.2.0...v2.2.1) - 2022-01-25

### Added

- Registered blade directives `admin` and `isRole`

### Changed

- Added condition to show additional information in translation table only for users with **SuperAdmin** role

## [v2.2.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.2.3...v2.2.0) - 2022-01-24

### Added

- Added default most user columns presets (methods)
- Added displaying some debug information about translations

### Changed

- Updated base translations table module, updated code and views

### Removed

- Removed overhead for using `permission` class constants. Reason - they rarely or not at all changed.

## [v2.2.3](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.1.2...v2.2.3) - 2022-01-23

### Added

- Registered **setup project** command to use

### Changed

- Updated file `.npmignore`

## [v2.1.2](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.1.1...v2.1.2) - 2022-01-23

### Added

- Added to composer `laravel/helpers` package
- Added npm files (package.json)
- Added fronted assets and files

## [v2.1.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.1.0...v2.1.1) - 2022-01-21

> Many changes, see comparison - [v2.1.0...v2.1.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.1.0...v2.1.1)

## [v2.1.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.0.1...v2.1.0) - 2022-01-16

### Fixed

- Fixed generate module command

### Changed

- Updated command for creating admin users
- Changed migrations files to anonymous classes
- Updated template files

## [v2.0.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.0.0...v2.0.1) - 2022-01-10

> Many changes, see comparison - [v2.0.0...v2.0.1](https://github.com/hexidedigital/hexide-admin-panel/compare/v2.0.0...v2.0.1)

## [v2.0.0](https://github.com/hexidedigital/hexide-admin-panel/compare/v1.0.2.3...v2.0.0) - 2022-01-02

### Changed

- Changed config file name

We just started maintaining a changelog starting from v2.
