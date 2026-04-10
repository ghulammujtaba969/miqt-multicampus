# MIQT System

MIQT System is a custom PHP/MySQL web application for **Minhaj Institute of Qirat & Tajweed**. It is built as a modular, non-framework school management system focused on student records, Quran progress tracking, attendance, exams, behavior reporting, academic calendar management, and printable reports.

## Project Overview

The application is designed for institute administration and daily academic operations. It supports role-based access and includes workflows for:

- student management
- parent records
- teacher and staff management
- student and teacher attendance
- Quran progress tracking
- exams and result management
- behavior reports
- academic calendar events
- PDF and DOCX exports
- bulk class and school class assignments

## Core Features

### Authentication and roles

- Session-based login from `index.php`
- Role-aware dashboards
- Roles currently used in the project include:
  - principal
  - vice_principal
  - coordinator
  - teacher
  - student
  - parent
  - staff
  - admin

### Student management

- Add, edit, view, import, export, and organize students
- Student profile pages with academic and contact details
- Optional login creation for students
- Support for:
  - guardian and contact info
  - mother/father details
  - admission challan details
  - previous school and result details
  - leaving details
  - uploaded student photo
  - uploaded previous result card

### Parent management

- Add, edit, and view parent records
- Parent dashboards
- Parent-to-student relationship support in the database

### HR management

- Add, edit, view, and manage teachers
- Add, edit, and manage staff accounts
- Teacher profile details including:
  - qualification
  - specialization
  - employment type
  - salary
  - reference details
  - past history

### Attendance

- Student attendance
- Teacher attendance
- Attendance reporting
- Attendance exports

### Quran progress

- Sabak entry
- Sabqi entry
- Manzil entry
- Student-wise and class-wise progress reports
- Daily unified progress workflows

### Exams and results

- Exam types
- Exam creation
- Result entry
- Result viewing
- Result cards
- Exam reports

### Behavior module

- Add behavior reports
- View behavior reports by student and class

### Academic calendar

- Add and manage academic events
- Calendar view
- Upcoming events listing

### Reporting and exports

- Attendance reports
- Progress reports
- Exam reports
- Daily, weekly, monthly, and yearly summaries
- PDF generation using `mpdf/mpdf`
- DOCX export helpers in `libs/`

### Bulk assignment tools

- Bulk assign current classes to multiple students
- Bulk assign school classes to multiple students
- Search and advanced filter support on bulk pages
- Hidden advanced search panels with field-level filtering

## Project Structure

Main directories:

- `config/`
  - application configuration
  - database connection
- `includes/`
  - shared layout and utility functions
- `modules/`
  - main feature modules
- `libs/`
  - PDF and DOCX helper classes
- `db/`
  - schema files and migrations
- `uploads/`
  - uploaded photos and import files
- `assets/`
  - CSS and JavaScript
- `vendor/`
  - Composer dependencies

Feature modules inside `modules/`:

- `auth/`
- `attendance/`
- `behavior/`
- `calendar/`
- `dashboard/`
- `exams/`
- `hr/`
- `progress/`
- `quran/`
- `reports/`
- `settings/`
- `students/`

## Technology Stack

- PHP
- MySQL / MariaDB
- PDO for database access
- HTML / CSS / JavaScript
- Bootstrap for UI support
- Font Awesome for icons
- `mpdf/mpdf` for PDF export

Composer dependency:

```json
{
  "require": {
    "mpdf/mpdf": "*"
  }
}
```

## Configuration

Primary config file:

- `config/config.php`

Current local configuration values in the project:

- database host: `localhost`
- database user: `root`
- database name: `u921830511_miqt`
- site URL: `http://localhost/miqt/02-04-2026-miqt/`
- timezone: `Asia/Karachi`

## Database Notes

The project contains more than one SQL source:

- `db/miqt_database.sql`
- `u921830511_miqt_new (2).sql`
- `students.sql`
- migration files under `db/migrations/`

Important note:

- The codebase has evolved beyond the older base schema in `db/miqt_database.sql`.
- The newer dump and migrations better reflect the current application behavior, especially for:
  - student roles
  - parent roles
  - staff roles
  - extended student fields
  - school class support via `current_school_class`

If you set up a fresh environment, verify that the imported database matches the code currently in use.

## Recent Custom Work Added

Recent project additions include:

- `modules/students/bulk_assign_classes.php`
  - bulk assignment of current class (`class_id`)
- `modules/students/bulk_assign_school_classes.php`
  - bulk assignment of school class (`current_school_class`)
- `current_school_class` converted from free text to class ID selection in:
  - `modules/students/add_student.php`
  - `modules/students/edit_student.php`
  - `modules/students/view_student.php`

## Setup

Typical local setup steps:

1. Place the project inside your local PHP server root.
2. Import the correct MySQL database.
3. Update `config/config.php` if your database name, URL, or credentials differ.
4. Install Composer dependencies if needed:

```bash
composer install
```

5. Make sure the `uploads/` folders are writable.
6. Open the project in the browser using the configured local URL.

## Key Files

- `index.php`
  - login page
- `config/config.php`
  - application settings
- `config/database.php`
  - PDO connection wrapper
- `includes/functions.php`
  - shared helper functions
- `includes/header.php`
  - sidebar and layout shell
- `AGENT_CHANGELOG.md`
  - detailed project change log maintained during ongoing work

## Known Maintenance Considerations

Areas that need care during future development:

- keep SQL dumps and live schema aligned
- avoid introducing more schema drift
- review upload validation and request security carefully
- keep role checks consistent across new pages
- verify export/report pages have the correct access controls

## Change Tracking

Project changes recorded during this working phase are maintained in:

- `AGENT_CHANGELOG.md`

That file should be updated whenever project behavior, schema usage, UI flows, or file structure changes.
