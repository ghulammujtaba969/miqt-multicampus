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
    <title>Login - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" type="image/png" href="<?php echo MIQT_LOGO_URL; ?>">
    <link rel="apple-touch-icon" href="<?php echo MIQT_LOGO_URL; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/theme-primary.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="miqt-login-body">

    <div class="left-panel">
        <div class="geo-pattern"></div>
        <div class="geo-circles"></div>
        <div class="left-content">
            <img class="mosque-icon" src="<?php echo MIQT_LOGO_URL; ?>" alt="MIQT logo" width="96" height="96" decoding="async">
            <div class="brand-name">MIQT <span>System</span></div>
            <div class="brand-tagline">Minhaj Institute of Qirat &amp; Tajweed</div>
            <div class="divider-ornament">✦</div>
            <ul class="features-list">
                <li>Comprehensive Student Management</li>
                <li>Quran Progress Tracking</li>
                <li>Attendance &amp; Behaviour Records</li>
                <li>Exams, Results &amp; Reports</li>
                <li>HR &amp; Academic Calendar</li>
            </ul>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-box">
            <div class="institute-badge"><img class="institute-badge-logo" src="<?php echo MIQT_LOGO_URL; ?>" alt="" width="22" height="22" decoding="async"> Minhaj Institute of Qira'at &amp; Tehfeez-ul-Quran</div>
            <h2 class="login-heading">Welcome Back</h2>
            <p class="login-subheading">Sign in to your account to continue</p>

            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="on">
                <div class="mb-3">
                    <label class="form-label miqt-label" for="username">Username</label>
                    <div class="input-icon-wrap">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required autofocus>
                        <span class="icon"><i class="fas fa-user"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label miqt-label" for="password">Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <span class="icon"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
                <button type="submit" class="btn-login">Login <i class="fas fa-arrow-right ms-1"></i></button>
            </form>

            <div class="credentials-box">
                <div class="cred-title">Default credentials</div>
                <p>Username: <strong>principal / vice_principal / coordinator</strong></p>
                <p>Password: <strong>admin123</strong></p>
                <p style="margin-top:8px;color:#888;font-size:0.78rem;">All users (Admin, Teacher, Student, Parent) use this page to sign in.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
