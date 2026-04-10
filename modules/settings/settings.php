<?php
/**
 * System Settings
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'System Settings';

// Get current settings
$sql = "SELECT * FROM settings LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute();
$settings = $stmt->fetch();

// If no settings exist, create default
if (!$settings) {
    $sql = "INSERT INTO settings (school_name, school_address, school_phone, school_email, academic_year, created_at)
            VALUES ('MINHAJ INSTITUTE OF QIRAT & TAJWEED', '', '', '', ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([date('Y')]);

    $sql = "SELECT * FROM settings LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $settings = $stmt->fetch();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_name = sanitize($_POST['school_name']);
    $school_address = sanitize($_POST['school_address']);
    $school_phone = sanitize($_POST['school_phone']);
    $school_email = sanitize($_POST['school_email']);
    $academic_year = sanitize($_POST['academic_year']);
    $attendance_required = isset($_POST['attendance_required']) ? 1 : 0;
    $progress_required = isset($_POST['progress_required']) ? 1 : 0;

    try {
        $sql = "UPDATE settings SET
                school_name = ?,
                school_address = ?,
                school_phone = ?,
                school_email = ?,
                academic_year = ?,
                attendance_required = ?,
                progress_required = ?,
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $school_name, $school_address, $school_phone, $school_email,
            $academic_year, $attendance_required, $progress_required,
            $settings['id']
        ]);

        logActivity($db, 'Update Settings', 'Settings', 'Updated system settings');
        setFlash('success', 'Settings updated successfully!');
        redirect(SITE_URL . '/modules/settings/settings.php');
    } catch(PDOException $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get system statistics
$sql = "SELECT COUNT(*) FROM students WHERE status = 'active'";
$total_students = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM teachers WHERE status = 'active'";
$total_teachers = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM classes WHERE status = 'active'";
$total_classes = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM users";
$total_users = $db->query($sql)->fetchColumn();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog"></i> System Settings</h1>
    <p class="subtitle">Configure system settings and preferences</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- System Statistics -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_students; ?></h3>
            <p>Active Students</p>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_teachers; ?></h3>
            <p>Active Teachers</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-school"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_classes; ?></h3>
            <p>Active Classes</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-users-cog"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_users; ?></h3>
            <p>System Users</p>
        </div>
    </div>
</div>

<!-- Settings Form -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> School Information</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <h3 class="mb-20">Basic Information</h3>

            <div class="form-group">
                <label for="school_name">School Name *</label>
                <input type="text" name="school_name" id="school_name" class="form-control"
                       value="<?php echo htmlspecialchars($settings['school_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="school_address">School Address</label>
                <textarea name="school_address" id="school_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['school_address']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_phone">Phone Number</label>
                    <input type="tel" name="school_phone" id="school_phone" class="form-control"
                           value="<?php echo htmlspecialchars($settings['school_phone']); ?>">
                </div>

                <div class="form-group">
                    <label for="school_email">Email Address</label>
                    <input type="email" name="school_email" id="school_email" class="form-control"
                           value="<?php echo htmlspecialchars($settings['school_email']); ?>">
                </div>
            </div>

            <h3 class="mb-20 mt-30">Academic Settings</h3>

            <div class="form-group">
                <label for="academic_year">Academic Year *</label>
                <input type="number" name="academic_year" id="academic_year" class="form-control"
                       value="<?php echo $settings['academic_year']; ?>" min="2020" max="2100" required>
            </div>

            <h3 class="mb-20 mt-30">System Preferences</h3>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="attendance_required"
                           <?php echo ($settings['attendance_required'] ?? 1) ? 'checked' : ''; ?>>
                    Require daily attendance marking
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="progress_required"
                           <?php echo ($settings['progress_required'] ?? 1) ? 'checked' : ''; ?>>
                    Require daily progress tracking
                </label>
            </div>

            <div class="form-group mt-30">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Additional Settings -->
<div class="dashboard-grid mt-20">
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-database"></i> Database Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Database Name:</th>
                    <td><?php echo DB_NAME; ?></td>
                </tr>
                <tr>
                    <th>Database Host:</th>
                    <td><?php echo DB_HOST; ?></td>
                </tr>
                <tr>
                    <th>PHP Version:</th>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <th>System URL:</th>
                    <td><?php echo SITE_URL; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> System Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>System Version:</th>
                    <td>1.0.0</td>
                </tr>
                <tr>
                    <th>Last Updated:</th>
                    <td><?php echo formatDate($settings['updated_at'] ?? $settings['created_at']); ?></td>
                </tr>
                <tr>
                    <th>Current Date:</th>
                    <td><?php echo date('l, F d, Y'); ?></td>
                </tr>
                <tr>
                    <th>Server Time:</th>
                    <td><?php echo date('h:i:s A'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
