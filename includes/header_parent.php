<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() != 'parent') {
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
                <p class="text-xs">Parent</p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo SITE_URL; ?>/modules/dashboard/parent_dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <?php if (isset($_SESSION['parent_id'])): 
                    // Get children IDs
                    $sql = "SELECT student_id FROM parent_student_relation WHERE parent_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$_SESSION['parent_id']]);
                    $children = $stmt->fetchAll();
                ?>
                <!-- My Children -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-user-graduate"></i> My Children
                    </a>
                    <ul class="submenu">
                        <?php foreach ($children as $child): 
                            $sql = "SELECT id, first_name, last_name, student_id FROM students WHERE id = ?";
                            $stmt2 = $db->prepare($sql);
                            $stmt2->execute([$child['student_id']]);
                            $student = $stmt2->fetch();
                            if ($student):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student['id']; ?>">
                                <?php echo $student['first_name'] . ' ' . $student['last_name']; ?> (<?php echo $student['student_id']; ?>)
                            </a>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </li>

                <!-- Children Progress -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-book-quran"></i> Progress Reports
                    </a>
                    <ul class="submenu">
                        <?php foreach ($children as $child): 
                            $sql = "SELECT id, first_name, last_name FROM students WHERE id = ?";
                            $stmt2 = $db->prepare($sql);
                            $stmt2->execute([$child['student_id']]);
                            $student = $stmt2->fetch();
                            if ($student):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student['id']; ?>#progress">
                                <?php echo $student['first_name']; ?> - Progress
                            </a>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </li>

                <!-- Children Exam Results -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-file-alt"></i> Exam Results
                    </a>
                    <ul class="submenu">
                        <?php foreach ($children as $child): 
                            $sql = "SELECT id, first_name, last_name FROM students WHERE id = ?";
                            $stmt2 = $db->prepare($sql);
                            $stmt2->execute([$child['student_id']]);
                            $student = $stmt2->fetch();
                            if ($student):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student['id']; ?>#exams">
                                <?php echo $student['first_name']; ?> - Results
                            </a>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </li>

                <!-- Children Attendance -->
                <li>
                    <a href="#" class="has-dropdown">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                    <ul class="submenu">
                        <?php foreach ($children as $child): 
                            $sql = "SELECT id, first_name, last_name FROM students WHERE id = ?";
                            $stmt2 = $db->prepare($sql);
                            $stmt2->execute([$child['student_id']]);
                            $student = $stmt2->fetch();
                            if ($student):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student['id']; ?>#attendance">
                                <?php echo $student['first_name']; ?> - Attendance
                            </a>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
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

