# Agent Change Log

This file records project changes made from April 10, 2026 onward, including recent changes already completed in this working session.

## 2026-04-10

### Added bulk student class assignment page

Files changed:
- `modules/students/bulk_assign_classes.php`
- `includes/header.php`

Details:
- Created a new admin-only page for bulk assigning classes to multiple students.
- Added student search by general text fields such as student ID, admission number, names, father name, phone, city, and CNIC/B-Form.
- Added class-based filtering using existing records from the `classes` table.
- Added a bulk action area that lets the user select multiple students and assign one class to all selected students in a single submission.
- Added a "Select all visible students" control and a live selected-count indicator.
- Added a hidden-by-default advanced search panel that opens with a button.
- Added advanced search rules that allow filtering by many `students` table fields, including checks like:
  - field contains value
  - field equals value
  - field starts with value
  - field is empty
  - field is not empty
- Added extra advanced filters for status, gender, student type, and "unassigned only".
- Added the navigation link below the Academic Calendar section in the sidebar.

### Adjusted bulk assignment table columns

Files changed:
- `modules/students/bulk_assign_classes.php`

Details:
- Added `admission_date` to the bulk assignment student table.
- Later removed the Date of Birth column from the same table to make the list cleaner.
- Updated the table empty-state column count after removing the DOB column.
- Final table includes:
  - select checkbox
  - student ID
  - admission number
  - name
  - father name
  - current class
  - admission date
  - guardian
  - phone
  - status

### Converted `current_school_class` to use real class IDs

Files changed:
- `modules/students/add_student.php`
- `modules/students/edit_student.php`
- `modules/students/view_student.php`

Details:
- Replaced the free-text `current_school_class` input in student add/edit forms with a dropdown populated from the `classes` table.
- Changed saving behavior so the selected class `id` is stored in the `current_school_class` column.
- Updated edit form loading so the previously saved class ID is pre-selected.
- Updated student profile view so it shows the related class name instead of the raw stored ID.
- Added a join to the `classes` table in the student profile and edit queries to resolve the saved `current_school_class` ID into a readable class name where needed.

### Verification performed

Details:
- Ran PHP syntax checks after the changes above.
- Verified no syntax errors in:
  - `modules/students/bulk_assign_classes.php`
  - `includes/header.php`
  - `modules/students/add_student.php`
  - `modules/students/edit_student.php`
  - `modules/students/view_student.php`

## Logging rule for future changes

For each future project change, this log should include:
- date
- files changed
- what was added, removed, or modified
- any data model or behavior changes
- any validation or verification performed

## 2026-04-10

### Added bulk school class assignment page

Files changed:
- `modules/students/bulk_assign_school_classes.php`
- `includes/header.php`

Details:
- Created a new admin-only page for bulk assigning school classes to students.
- Modeled the page after the bulk class assignment screen for a consistent workflow.
- Configured the page to write selected class IDs into the `students.current_school_class` column.
- Added filtering by current school class and current assigned class.
- Added advanced search support using whitelisted `students` fields, including checks for empty and non-empty values.
- Added bulk selection, assign action, and a live selected-count helper.
- Added a sidebar link named `Bulk School Class Assign` alongside the existing bulk class assignment link.

### Data display behavior for bulk school class page

Files changed:
- `modules/students/bulk_assign_school_classes.php`

Details:
- Added both `class_name` and `current_school_class_name` to the listing query by joining the `classes` table twice.
- Displayed both:
  - current assigned class (`class_id`)
  - current school class (`current_school_class`)
- Kept admission date, guardian, phone, and status visible to help operators verify the right student rows before bulk updates.

### Added project README

Files changed:
- `README.md`

Details:
- Created a root-level project README for onboarding and project reference.
- Documented:
  - project purpose
  - main features
  - module list
  - technology stack
  - configuration notes
  - database notes
  - recent custom additions
  - setup guidance
  - key files
  - maintenance considerations
  - change tracking location

## 2026-04-10 — MIQT Primary theme, new pages, and configuration

### Theme and assets

Files changed (representative):

- `assets/css/theme-primary.css` — MIQT palette, app shell, login split layout, dashboard/students components, Bootstrap button overrides, table action styling
- `assets/css/theme-pages.css` — calendar, email demo, profile, settings hub, `miqt-form-section` form cards
- `assets/css/style.css` — alignment with theme variables and sidebar logo behavior
- `includes/header.php`, `includes/header_teacher.php`, `includes/header_student.php`, `includes/header_parent.php` — fonts, theme CSS order, favicon/logo (`MIQT_LOGO_URL`), sidebar entries (Profile, Email where permitted)
- `includes/footer.php`, `assets/js/main.js` — sidebar toggle / accessibility fixes
- `includes/functions.php` — `getUserInitials()` for avatars
- `index.php` — themed login; logo/favicon
- `modules/dashboard/index.php`, `modules/students/students.php` — themed layouts

Details:

- Design reference: `themes/primary` HTML mocks (login, dashboard, students, calendar, email, forms, profile, settings).
- Logo path: `assets/img/favicon.png` exposed as `MIQT_LOGO_URL` in `config/config.php`.

### Calendar, settings, email, profile

Files changed:

- `modules/calendar/index.php` — month grid, mini calendar, upcoming events, themed controls
- `modules/settings/settings.php` — left nav sections; institute form still POSTs to `settings` table; other sections mix live stats, reference UI, and placeholders
- `modules/email/index.php` (new) — demo inbox UI only (principal / vice_principal / coordinator)
- `modules/profile/index.php` (new) — session user + optional `teachers` row; tabs; HR links when permitted

### Student and HR forms (theme form sections)

Files changed:

- `modules/students/add_student.php`, `modules/students/edit_student.php`
- `modules/hr/add_staff.php`, `modules/hr/edit_staff.php`, `modules/hr/add_teacher.php`, `modules/hr/edit_teacher.php`

Details:

- Replaced flat `dashboard-card` layout with `miqt-forms-page-header`, breadcrumbs, and `miqt-form-section` blocks matching the Forms mock.

### Configuration and deployment

Files changed:

- `config/config.php` — optional `config.local.php` load; conditional DB/SITE defaults; `SITE_URL` from `MIQT_SITE_URL` or auto-detect (HTTPS, host, path under `DOCUMENT_ROOT`); `MIQT_LOGO_URL`; local vs production `display_errors` / `log_errors`
- `config/database.php` — `require_once __DIR__ . '/config.php'`
- `config/config.local.example.php` — template for live-only `config.local.php` (placeholders only)
- `.gitignore` — `config/config.local.php` excluded from commits

Details:

- Production credentials belong in `config/config.local.php` on the server only, not in the shared repo copy.
- Resolves common “blank / unstyled page” after deploy when `SITE_URL` still pointed at localhost.

### Documentation

Files changed:

- `README.md` — technology stack, assets, modules (`email`, `profile`), configuration (`config.local`, auto `SITE_URL`, error logging), recent custom work, setup step for production
- `AGENT_CHANGELOG.md` — this section (theme, calendar/settings/email/profile, themed forms, deployment config, doc refresh)

### Verification performed

Details:

- `php -l` on touched PHP modules during theme and config work (no syntax errors reported in session).
