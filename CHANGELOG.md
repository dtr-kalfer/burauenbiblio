# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/).

Guide:
- version 3.x.x above PHP 8.0 supported
- version 2.x.x below PHP 7.4.26 supported

## [unreleased] - 2025-xx-xx
### Added
- Total barcode count in reports menu.

### Changed
- Improved book barcode search entry, only 13 digits allowed, no text.
- Improved add new copy entry on barcode, only 13 digits allowed, no text.

### Fixed
- Admin Biblio Copy Fields, 'Add new' button fixed.
- OPAC load on startup warning.
- Fixed deprecated syntax / Undefined arrays.
- 'Update copy' error fixed, when changing field details.
- Fixed Tag/untagged items on OPAC, must not show.


## [3.1.0] - 2025-06-1
### Changed
- Check and set active on PHP 8.0.30
- Dropdown select menu on media type/collection.

### Fixed
- Deprecated issues and the card catalog print error on call number.


## [3.0.0] - 2025-05-31
### Added
- BurauenBiblio is now compatible with PHP 8.0.13

### Changed
- Refactor code / deprecated php syntax work-in-progress compatible with PHP 8.0
- Changed UI setup for some navigation and tabs.

### Fixed
- Fixed several deprecated/undefined errors for the stricter PHP 8.0.
- Print card catalog error.



## [2.3.0] - 2025-05-28
### Added
- App. Info tab feature. Shows PHP version, database, web server and system resources.
- 'Option to disable/enable 'Delete Button' according to assigned staff 'roles'.

### Changed
- Improve staff form user interface.

### Fixed
- 'Paste' or CTRL+V now works on cataloging. This has bothered me for ages!
- 'Existing items' return reponse in navigation menu.


---

## [2.2.1] - 2025-05-27
### Changed
- Replaced 'Toggle roles' checkbox with a select dropdown in the Staff Form.
- Refactored role logic for better maintainability.
- Minor UI tweaks on the staff settings form.

## [2.1.0] - 2025-05-26
### Added
- Member Transaction functionality with payment, charge, and credit modes.
- New module to manage library obligations and patron penalties.

## [2.0.2] - 2025-04-26
### Fixed
- Redirect bug when adding a new member (Page not found issue).

## [2.0.1] - 2025-05-25
### Fixed
- Corrected mislabeled 'Add list to cart' button. Use Tagged Items section.

## [2.0.0] - 2025-05-25
### Added
- Card Catalog Print: Generate print-friendly formats for offline archiving.
- Online Catalog Search: Resolved LoC (Library of Congress) z39.50/SRU integration.
- Tagged Items: Allow staff to mark biblios for review or correction.
- Reservations System: Patron-driven reservation request with staff approval.
- MARC Import Tool: Bulk add MARC entries with validation.

### Improvements
- Enhanced provincial and UAC address logic for patron registration.
- Refactored borrowing policy with customizable time limits.
- Search results now display bibid, accurate copy count, and better UI.

