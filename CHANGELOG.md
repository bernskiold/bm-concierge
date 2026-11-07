# Changelog

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.0.0 - 2026-11-07

### Requirements

- **PHP 8.2** or later and **WordPress 7.0** or later are now required. Tested up to WordPress 7.1.

### Changed

- Plugin updates use `yahnis-elsts/plugin-update-checker` v6 from Composer, replacing the bundled v4 copy.
- `deeplcom/deepl-php` 0.4 → 1.21.
- The "open Concierge" button opens the sidebar through the `core/interface` store.
- Release ZIPs include the production `vendor/`.

### Development

- Node 26 and `@wordpress/scripts` 37; WordPress Coding Standards 3; PHPUnit on PHP 8.2–8.5 × WordPress 7.0 and latest via the shared Bernskiold workflows.

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
