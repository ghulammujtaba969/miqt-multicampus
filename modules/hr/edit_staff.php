<?php
/**
 * Edit Staff
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Staff';

$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get staff details from users table
$sql = "SELECT * FROM users WHERE id = ? AND role IN ('staff', 'principal', 'vice_principal', 'coordinator')";
$stmt = $db->prepare($sql);
$stmt->execute([$staff_id]);
$staff = $stmt->fetch();

if (!$staff) {
    setFlash('danger', 'Staff member not found');
    redirect(SITE_URL . '/modules/hr/staff.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'] ?? '';
    $email = sanitize($_POST['email'] ?? '');
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role'] ?? 'staff');
    $status = sanitize($_POST['status'] ?? 'active');
    
    if (empty($username) || empty($full_name)) {
        $error = 'Username and full name are required';
    } else {
        // Check if username exists for another user
        $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username, $staff_id]);
        if ($stmt->fetch()) {
            $error = 'Username already exists';
        }
    }

    if (empty($error)) {
        try {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username = ?, password = ?, email = ?, full_name = ?, role = ?, status = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username, $hashed_password, $email, $full_name, $role, $status, $staff_id]);
            } else {
                $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, status = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username, $email, $full_name, $role, $status, $staff_id]);
            }

            logActivity($db, 'Edit Staff', 'HR', "Updated staff: $full_name (Role: $role)");
            setFlash('success', 'Staff member updated successfully!');
            redirect(SITE_URL . '/modules/hr/view_staff.php?id=' . $staff_id);
        } catch(PDOException $e) {
            $error = 'Error updating staff: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="miqt-forms-page-header">
    <h2>Edit staff</h2>
    <div class="miqt-breadcrumb-custom">
        <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php">Dashboard</a>
        <span class="sep">›</span>
        <a href="staff.php">Staff</a>
        <span class="sep">›</span>
        <a href="view_staff.php?id=<?php echo (int)$staff_id; ?>">View</a>
        <span class="sep">›</span>
        <span>Edit</span>
    </div>
</div>
<p class="text-muted small mb-3"><?php echo htmlspecialchars($staff['username']); ?> — <?php echo htmlspecialchars($staff['full_name']); ?></p>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="">
<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-user-tie"></i></div>
        <div>
            <h3>Staff account</h3>
            <p>Update login, role, and status</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" class="form-control"
                           value="<?php echo htmlspecialchars($staff['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password (leave empty to keep current)</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" class="form-control"
                           value="<?php echo htmlspecialchars($staff['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role">Role *</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="staff" <?php echo ($staff['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                        <option value="principal" <?php echo ($staff['role'] == 'principal') ? 'selected' : ''; ?>>Principal</option>
                        <option value="vice_principal" <?php echo ($staff['role'] == 'vice_principal') ? 'selected' : ''; ?>>Vice Principal</option>
                        <option value="coordinator" <?php echo ($staff['role'] == 'coordinator') ? 'selected' : ''; ?>>Coordinator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($staff['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($staff['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="miqt-form-actions-bar mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update staff
                </button>
                <a href="view_staff.php?id=<?php echo $staff_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
    </div>
</div>
</form>

<?php require_once '../../includes/footer.php'; ?>

