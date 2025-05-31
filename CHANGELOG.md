# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/).

Guide:
version 3.x.x --> PHP 8.0 supported
version 2.x.x --> PHP 7.4.26 supported

## [unreleased]
### Added
- 

### Changed
- Changed deprecated syntax/logic PART 2 of 3
- Changed deprecated syntax/logic PART 1 of 3

### Fixed
- Preparation for PHP 8.0 support by fixing several deprecated/undefined errors.
- Print card catalog error. Occurs when call number is missed out or different standard.



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

