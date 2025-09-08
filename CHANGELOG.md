# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/).

Guide:
- version 3.x.x above PHP 8.0.xx supported
- version 2.x.x below PHP 7.4.26 supported

## [unreleased] - xxxx-yy-zz
### Added 
- New feature: Database Migration Manager, utility to safely migrate old Openbiblio 1.0 database. (Please make a backup before attempt)

### Changed
- Update Daily Tally to include 'Show most recently added books' on reshelving process.
- Refactor: Attendance chart improved instead of using a separate file courses.txt

## [3.9.5] - 2025-08-30
### Added 
- New feature: Check and remove orphaned bibIds utility.
- New feature: Advanced Dewey Decimal Classification Mapping (Class, Division and Topic Mapping).
- Top 30 DDC Chart and Top 30 DDC table listing.
- New feature: DDC Create Table using basic ten main classes.
- New feature: DDC Chart from table with JSON export.
- DOI example and improved UI.

### Changed
- Restored DOI search into the OPAC setup.
- DOI search removed from Cataloging menu.
- Updated To-do List.
- Updated sql data.
- Set z39.50 default online setup to Dewey (DDC).

### Fixed
- n.a.

## [3.8.15] - 2025-08-16
### Added
- For new biblio records, book title is auto-copied into the copy description, no need to retype entire title.

### Changed
- Card catalog print extended to support 35 barcode copies (5 columns).
- Improved Print Card Catalog UI display with help info for MARC details.

### Fixed
- Error on Card catalog print negative number on too lengthy inputs.
- Fixed: Library Staff no longer need to retype the title into copy description field.

## [3.7.14] - 2025-08-14
### Added
- Added graph display for In-house Book Activity, span of one week.
- Added Export to JSON, In-house Book Activity data.
- Added In-House Book Activity Tracker to Identify high-interest books for future collection development.
- Table result for In-House Book Activity Tracker via Top 30.
- Added JSON export feature for Circulation record check-in and check-outs.
- Added 'Thumbnail check' in cataloging navigation, check missing thumbnail in biblio records.
- Added JSON export feature for Library Attenance record with student only selection.
- Exclude on bar graph both faculty and visitor on Update chart button, show only student attendees.
- Added 🐶🐕 'Guard doggy' security logic. 

### Changed
- Refactor: Print card catalog to use MARC tags instead of sequential subfield data.
- Changed attendance form UI and attendance chart UI with.
- Set default 'circ. export to JSON' value 'current month' and '12 months ago'.
- Improved both top 30 results to include Author and ISBN.

### Fixed
- Update: more locale definitions for other foreign language in future BurauenBiblio support.

## [3.6.2] - 2025-07-25
### Added
- chart.js - MIT license free for commercial/non-commercial use
- Analytics: Monthly circulation report with input range feature
- Analytics: Top 30 active books
- Analytics: Library attendance tracking with input range feature
- Added 'easy' feature change label / select list by editing courses.txt for attendance graphs

### Changed
- Updated navigation menu: Top 30 active books
- Updated navigation menu: Circ. Report
- Changed input Library Attendance and Monthly circ. report into dynamic setup.
- Changed navigation sidebar added new link for new features.
- updated sql database with graph/course fictional attendance data.

### Fixed
- Fixed result generated from Top 30 books.
- Fixed To-do list multiple clicks, added usleep and htmx hx-disabled-elt on todo actions.
- Fixed validation features for month span input on circ. report and attendance.

## [3.5.0] - 2025-07-09

### Added
- Full integration To-do list feature with mysql instead of using sqlite.
- Scalable Todo-list, allow concurrent due to mysql nature.

### Changed
- Changed sql structure to align with To-do list feature. Use this to support To-do list with mysql benefits.

### Fixed
- Fixed error on barcode check-in, happens when call a method insert() on a variable that's not supposed to be a string.
- Fixed no response bug on To-do list on consecutive action.

## [3.4.2] - 2025-07-08

### Added
- Added 'member type' column on member search result.

### Changed
- Added more emoji content on todo list UI.
- Improved UI for member search result.

### Fixed
- Fixed UI responsive for 'cataloging new items', after dropdown select, submit button is disabled.
- Fixed UI responsive for 'existing items', after dropdown select, submit button is disabled.
- Fixed: Can't use 'go back' button during 'add photo' setup on 'new items', under cataloging.

## [3.4.1] - 2025-07-06
### Added
- toggle delay when click 'add task' and use a spinner using HTMX.

### Changed
- Improved UI/UX on todo list menu.

### fixed
- fix can't delete button on todo list.

## [3.4.0] - 2025-07-05
### Added
- Added new feature 'Todo List', task collab with other library staff.
- Emoji list selection.
- Use htmx for todo_list.

### Changed
- Improved 'guard token' with other factors, use for htmx plus.

### Fixed
- Calendar response for 'Add Task'.

## [3.3.8] - 2025-07-05
### Added
- Added 'Guard token' for improved security, works best with htmx.
- Set a 10 second wait cycle period for invalid user/pass entries.
- Added a welcome landing page for both admin/staff upon success login.
- Use of Third-party jsCalendar for a future feature use.
- Front-end validation for required for add/update member fields, noted by *.

### Changed
- Improved help on members form, member types and collections menu.

### Fixed
- Correct message response for invalid username and pass, instead of nothing happened.

## [3.3.1] - 2025-06-25
### Added
- New stack using HTMX into card catalog error handling.
- Standalone help guide for checkout policy and due dates, under circulation/members.
- Pagination support (25 results per page) for 'Search Members' under 'Circulation'

### Changed
- simplified library settings.
- Updated due date logic, now included into booking table.
- Improved show date on checkouts menu.
- Improved show 'Searched' entry on member search.
- Refactored barcode search using equality instead of wildcard.
- Improved search speed i.e. LIKE '%digits' can't use indexes efficiently (from above).
- Checkout and booking barcode accept numeric inputs only, 13 digit.

### Fixed
- Error handling on bibID not found on card catalog print.
- Show 'cross_out.webp' overlay image on the closed calendar days in Calendar Menu.
- Compatible pagination of search members using original logic (doNameSearch) function.
- auto-barcode tested, barcode search fixed, no need to type the entire 13 digits, i.e. 0000000000007 just 7 

## [3.2.0] - 2025-06-20
### Added
- Correct overdue date based on calendar menu, which accurately considers holidays and weekends (as set on the Calendar),
- Added a loan allotment, under member types.
- Loan allotment can be changed dynamically.
- Current dates show on member checkout and welcome after staff successful login.

### Changed
- Reduce clutter on bookings menu list.
- overdue menu shows a list of overdue items.
- .sql file is updated, mbr_classify_dm increase max_fines value to 9999.99.
- .sql file is updated, mbr_classify_dm ADD loan_allotment, for a new feature.

### Fixed
- overdue menu gives out correct list of overdue items/patrons, tested on actual overdue records.
- 'Late rate fee' for overdue books working.
- 'Max fines' now work and halt borrow if fines are exceeded, depends on member type.
- loan allotment only accepts integer values max. 3 digits.

## [3.1.1] - 2025-06-07
### Added
- Total barcode count (Accession) in reports menu.
- Included burauenbib_sample_clean.sql as data sample.

### Changed
- Improved book barcode search entry, only 13 digits allowed, no text.
- Improved add new copy entry on barcode, only 13 digits allowed, no text.

### Fixed
- Admin Biblio Copy Fields, 'Add new' button fixed.
- Calendar UI fixed redundant calendars.
- OPAC load on startup warning.
- Fixed deprecated syntax / Undefined arrays.
- 'Update copy' error fixed, when changing field details.
- Fixed Tag/untagged items on OPAC, must not show.


## [3.1.0] - 2025-06-01
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

