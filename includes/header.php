<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}
if (!isset($pageTitle)) {
    $pageTitle = '';
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle !== '' ? htmlspecialchars($pageTitle) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,700;1,400&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/theme-primary.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="miqt-app">
    <div class="wrapper">
        <nav class="sidebar" id="miqt-sidebar">
            <div class="sb-brand">
                <div class="logo-sq" aria-hidden="true">🕌</div>
                <div class="brand-text">
                    <div class="name">MIQT System</div>
                    <div class="sub">Islamic Institute</div>
                </div>
            </div>
            <div class="sb-user">
                <div class="avi" title="<?php echo htmlspecialchars(getUserName() ?? ''); ?>"><?php echo htmlspecialchars(getUserInitials()); ?></div>
                <div class="u-meta">
                    <div class="u-name"><?php echo htmlspecialchars(getUserName() ?? ''); ?></div>
                    <div class="u-role"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', getUserRole() ?? ''))); ?></div>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sb-section-label-wrap"><span class="sb-section-label">Navigation</span></li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-users"></i> HR Management
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/hr/teachers.php">Teachers</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/hr/add_teacher.php">Add Teacher</a></li>
                        <li><hr style="margin: 5px 0;"></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/hr/staff.php">Staff</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/hr/add_staff.php">Add Staff</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/students.php">All Students</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/alumni.php">Alumni</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/left_expelled.php">Left / Expelled</a></li>
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/add_student.php">Add Student</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/import_students.php">Import Students</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/classes.php">Classes</a></li>
                        <li><hr style="margin: 5px 0;"></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/parents.php">Parents</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/add_parent.php">Add Parent</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/export_students.php?status=all">Export Students</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/attendance/student_attendance.php">Student Attendance</a></li>
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/attendance/teacher_attendance.php">Teacher Attendance</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/attendance/view_attendance.php">View Reports</a></li>
                    </ul>
                </li>

                <li class="sb-section-label-wrap"><span class="sb-section-label">Academic</span></li>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-user-check"></i> Behaviour
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/behavior/behavior_reports.php">All Behaviour Reports</a></li>
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator', 'teacher', 'admin'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/behavior/add_behavior_report.php">Add Behaviour Report</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-book-quran"></i> Quran Progress
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-star"></i> Daily Progress (Unified)</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_sabak.php">Add Sabak (New Lesson)</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_sabqi.php">Add Sabqi (Revision)</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_manzil.php">Add Manzil</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/view_progress.php">View Progress</a></li>
                    </ul>
                </li>

                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-book"></i> Quran Content
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/quran/manage_juz.php">Manage Juz/Parts</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/quran/manage_surahs.php">Manage Surahs</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/quran/manage_ayahs.php">Manage Ayahs</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-file-alt"></i> Exams &amp; Results
                    </a>
                    <ul class="submenu">
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/manage_exams.php">Manage Exams</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/manage_exam_types.php">Exam Types</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/add_results.php">Add Results</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/view_results.php">View Results</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/result_cards.php">Result Cards</a></li>
                    </ul>
                </li>

                <li class="sb-section-label-wrap"><span class="sb-section-label">Reports &amp; system</span></li>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <ul class="submenu">
                        <li><strong>Attendance</strong></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/attendance_classwise.php">Class Wise</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/attendance_studentwise.php">Student Wise</a></li>
                        <li><strong>Progress</strong></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/progress_classwise.php">Class Wise</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/progress_studentwise.php">Student Wise</a></li>
                        <li><strong>Exams</strong></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/exams_classwise.php">Class Wise</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/exams_studentwise.php">Student Wise</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/daily_report.php">Daily Summary</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/weekly_report.php">Weekly Summary</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/monthly_report.php">Monthly Summary</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/yearly_report.php">Yearly Summary</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-calendar-alt"></i> Academic Calendar
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/calendar/view_events.php">Upcoming Events</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/calendar/index.php">Calendar View</a></li>
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/modules/calendar/add_event.php">Add Event</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/calendar/manage_events.php">Manage Events</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/bulk_assign_classes.php">
                        <i class="fas fa-layer-group"></i> Bulk Class Assign
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/bulk_assign_school_classes.php">
                        <i class="fas fa-school"></i> Bulk School Class Assign
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission(['principal', 'vice_principal'])): ?>
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/settings/settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <?php endif; ?>

                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-left">
                    <div class="toggle-btn" id="sidebarToggle" role="button" tabindex="0" title="Toggle sidebar" aria-controls="miqt-sidebar" aria-expanded="true">
                        <i class="fas fa-bars"></i>
                    </div>
                    <h1 class="page-heading"><?php echo $pageTitle !== '' ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></h1>
                </div>
                <div class="tb-right">
                    <div class="tb-date"><i class="far fa-calendar-alt me-1"></i><?php echo date('l, F d, Y'); ?></div>
                </div>
            </div>

            <div class="content">
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>
