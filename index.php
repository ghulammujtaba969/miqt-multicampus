<?php
/**
 * Login Page
 * MIQT System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    $role = getUserRole();
    switch ($role) {
        case 'teacher':
            redirect(SITE_URL . '/modules/dashboard/teacher_dashboard.php');
            break;
        case 'student':
            redirect(SITE_URL . '/modules/dashboard/student_dashboard.php');
            break;
        case 'parent':
            redirect(SITE_URL . '/modules/dashboard/parent_dashboard.php');
            break;
        default:
            redirect(SITE_URL . '/modules/dashboard/index.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        // Check user credentials
        $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();

            // Set role-specific session variables
            if ($user['role'] == 'teacher') {
                // Get teacher ID
                $sql = "SELECT id FROM teachers WHERE user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$user['id']]);
                $teacher = $stmt->fetch();
                if ($teacher) {
                    $_SESSION['teacher_id'] = $teacher['id'];
                }
            } elseif ($user['role'] == 'student') {
                // Get student ID
                $sql = "SELECT id, class_id FROM students WHERE user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$user['id']]);
                $student = $stmt->fetch();
                if ($student) {
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['class_id'] = $student['class_id'];
                }
            } elseif ($user['role'] == 'parent') {
                // Get parent ID
                $sql = "SELECT id FROM parents WHERE user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$user['id']]);
                $parent = $stmt->fetch();
                if ($parent) {
                    $_SESSION['parent_id'] = $parent['id'];
                }
            }

            // Log activity
            logActivity($db, 'Login', 'Authentication', 'User logged in');

            // Redirect to appropriate dashboard based on role
            switch ($user['role']) {
                case 'teacher':
                    redirect(SITE_URL . '/modules/dashboard/teacher_dashboard.php');
                    break;
                case 'student':
                    redirect(SITE_URL . '/modules/dashboard/student_dashboard.php');
                    break;
                case 'parent':
                    redirect(SITE_URL . '/modules/dashboard/parent_dashboard.php');
                    break;
                default:
                    redirect(SITE_URL . '/modules/dashboard/index.php');
            }
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MIQT System</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-mosque"></i>
                <h1>MIQT System</h1>
                <p>Minhaj Institute of Qirat & Tajweed</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="login-footer">
                <p class="text-sm">Default Login Credentials:</p>
                <p class="text-xs">Username: <strong>principal</strong> / <strong>vice_principal</strong> / <strong>coordinator</strong></p>
                <p class="text-xs">Password: <strong>admin123</strong></p>
                <p class="text-xs" style="margin-top: 10px; color: #666;">
                    <i class="fas fa-info-circle"></i> All users (Admin, Teacher, Student, Parent) login through this page
                </p>
            </div>
        </div>
    </div>
</body>
</html>
