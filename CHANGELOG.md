# Changelog

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Requires PHP 8.2 and WordPress 7.0. Tested up to WordPress 7.1.
- The update checker is now `yahnis-elsts/plugin-update-checker` v6 from Composer, replacing the bundled v4 copy in `lib/` (PHP 8.4 deprecations, global `__NAMESPACE__` lookups).
- `deeplcom/deepl-php` 0.4 → 1.21.
- Node 26. PHP is linted with WordPress Coding Standards 3 (`phpcs.xml.dist`) instead of the archived `bernskioldmedia/phpcs-wordpress`.
- The "open Concierge" button opens the sidebar through the `core/interface` store rather than `core/edit-post`.

## [1.0.2] - 2021-09-05

### Fixed

- Editor settings would crash when attempting to open.

## [1.0.1] - 2021-08-24

### Added

- Auto-update functionality that ties in with WP updater.

## [1.0.0] - 2021-07-27

### Added

- Proofreading service
- Layout service
- On-Page SEO service
- Accessibility Audit service
