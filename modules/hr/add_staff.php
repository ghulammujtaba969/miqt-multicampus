<?php
/**
 * Add Staff
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Add Staff';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $email = sanitize($_POST['email'] ?? '');
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role'] ?? 'staff');
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = 'Username, password, and full name are required';
    } else {
        // Check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists';
        }
    }

    if (empty($error)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, email, full_name, role, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username, $hashed_password, $email, $full_name, $role, $status]);

            logActivity($db, 'Add Staff', 'HR', "Added staff: $full_name (Role: $role)");
            setFlash('success', 'Staff member added successfully!');
            redirect(SITE_URL . '/modules/hr/staff.php');
        } catch(PDOException $e) {
            $error = 'Error adding staff: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="miqt-forms-page-header">
    <h2>Add staff</h2>
    <div class="miqt-breadcrumb-custom">
        <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php">Dashboard</a>
        <span class="sep">›</span>
        <a href="staff.php">Staff</a>
        <span class="sep">›</span>
        <span>Add</span>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="">
<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-user-tie"></i></div>
        <div>
            <h3>Staff account</h3>
            <p>Login credentials and role for this user</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role">Role *</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="staff">Staff</option>
                        <option value="principal">Principal</option>
                        <option value="vice_principal">Vice Principal</option>
                        <option value="coordinator">Coordinator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="miqt-form-actions-bar mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add staff
                </button>
                <a href="staff.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
    </div>
</div>
</form>

<?php require_once '../../includes/footer.php'; ?>

