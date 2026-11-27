# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 27 November 2026
### Added
- Block admin POST requests that reach PHP `max_input_vars` or `max_input_nesting_level`: the admin is redirected back (or an AJAX call gets a JSON error) with a message naming the PHP setting to increase, instead of saving silently truncated data
- Only check POST requests of logged-in admin users, after the core form key (CSRF) validation, while keeping all core admin request validators (CSRF, HTTP method, backpressure) in place
- Unit and integration tests

### Changed
- Use the OSL-3.0 license and versioned Magento requirements in composer.json
