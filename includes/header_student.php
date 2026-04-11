<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() != 'student') {
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
                <p class="text-xs">Student</p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/dashboard/student_dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <?php if (isset($_SESSION['student_id'])): ?>
                <!-- My Profile -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $_SESSION['student_id']; ?>">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>

                <!-- My Progress -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $_SESSION['student_id']; ?>#progress">
                        <i class="fas fa-book-quran"></i> My Progress
                    </a>
                </li>

                <!-- My Attendance -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $_SESSION['student_id']; ?>#attendance">
                        <i class="fas fa-calendar-check"></i> My Attendance
                    </a>
                </li>

                <!-- My Exam Results -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $_SESSION['student_id']; ?>#exams">
                        <i class="fas fa-file-alt"></i> My Exam Results
                    </a>
                </li>

                <!-- My Behavior Reports -->
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $_SESSION['student_id']; ?>#behavior">
                        <i class="fas fa-user-check"></i> My Behavior Reports
                    </a>
                </li>
                <?php endif; ?>

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

