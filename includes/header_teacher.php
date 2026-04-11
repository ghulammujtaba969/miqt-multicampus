<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() != 'teacher') {
    redirect(SITE_URL . '/index.php');
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo MIQT_LOGO_URL; ?>">
    <link rel="apple-touch-icon" href="<?php echo MIQT_LOGO_URL; ?>">
    <!-- Bootstrap CSS (global) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- App styles (load after Bootstrap to allow overrides) -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo MIQT_LOGO_URL; ?>" alt="" class="sidebar-header-logo" width="48" height="48" decoding="async">
                <h3>MIQT System</h3>
                <p class="text-sm"><?php echo getUserName(); ?></p>
                <p class="text-xs">Teacher</p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/dashboard/teacher_dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <!-- My Students -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-user-graduate"></i> My Students
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/students/students.php">View Students</a></li>
                    </ul>
                </li>

                <!-- Attendance Module -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/attendance/student_attendance.php">Mark Attendance</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/attendance/view_attendance.php">View Reports</a></li>
                    </ul>
                </li>

                <!-- Progress Module -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-book-quran"></i> Quran Progress
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php">Daily Progress</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_sabak.php">Add Sabak</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_sabqi.php">Add Sabqi</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/add_manzil.php">Add Manzil</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/progress/view_progress.php">View Progress</a></li>
                    </ul>
                </li>

                <!-- Exams Module -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-file-alt"></i> Exams & Results
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/add_results.php">Add Results</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/exams/view_results.php">View Results</a></li>
                    </ul>
                </li>

                <!-- Behaviour Module -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-user-check"></i> Behaviour
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/behavior/behavior_reports.php">View Reports</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/behavior/add_behavior_report.php">Add Report</a></li>
                    </ul>
                </li>

                <!-- Reports Module -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/attendance_studentwise.php">Attendance Reports</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/progress_studentwise.php">Progress Reports</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/modules/reports/daily_report.php">Daily Summary</a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <div class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="user-info">
                    <span><?php echo date('l, F d, Y'); ?></span>
                </div>
            </div>

            <div class="content">
                <?php
                // Display flash messages
                $flash = getFlash();
                if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>

