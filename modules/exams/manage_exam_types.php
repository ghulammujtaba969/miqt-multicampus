<?php
/**
 * Manage Exam Types
 * Add and list exam types used by Exams
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Exam Types';

// Ensure table exists (safe guard)
try {
    $db->query("SELECT 1 FROM exam_types LIMIT 1");
} catch (PDOException $e) {
    $db->exec("CREATE TABLE IF NOT EXISTS exam_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_name VARCHAR(100) NOT NULL,
        exam_type ENUM('monthly','quarterly','half_yearly','annual','special') NOT NULL,
        description TEXT DEFAULT NULL,
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

// Handle Add/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_name = sanitize($_POST['exam_name'] ?? '');
    $exam_type = sanitize($_POST['exam_type'] ?? 'monthly');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (isset($_POST['add'])) {
        try {
            $stmt = $db->prepare("INSERT INTO exam_types (exam_name, exam_type, description, status) VALUES (?,?,?,?)");
            $stmt->execute([$exam_name, $exam_type, $description ?: null, $status]);
            setFlash('success', 'Exam type added');
            redirect(SITE_URL . '/modules/exams/manage_exam_types.php');
        } catch (PDOException $e) {
            $error = 'Error adding exam type: ' . $e->getMessage();
        }
    }

    if (isset($_POST['update'])) {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("UPDATE exam_types SET exam_name = ?, exam_type = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$exam_name, $exam_type, $description ?: null, $status, $id]);
            setFlash('success', 'Exam type updated');
            redirect(SITE_URL . '/modules/exams/manage_exam_types.php');
        } catch (PDOException $e) {
            $error = 'Error updating exam type: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM exam_types WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Exam type deleted');
        redirect(SITE_URL . '/modules/exams/manage_exam_types.php');
    } catch (PDOException $e) {
        $error = 'Error deleting exam type: ' . $e->getMessage();
    }
}

// Fetch all
$stmt = $db->prepare("SELECT * FROM exam_types ORDER BY status DESC, exam_name ASC");
$stmt->execute();
$types = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-tags"></i> Manage Exam Types</h1>
    <p class="subtitle">Create and manage exam categories</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/exams/manage_exams.php"><i class="fas fa-arrow-left"></i> Back to Exams</a></p>
    
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Add New Type</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="exam_name">Name *</label>
                    <input type="text" name="exam_name" id="exam_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="exam_type">Type *</label>
                    <select name="exam_type" id="exam_type" class="form-control" required>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="half_yearly">Half Yearly</option>
                        <option value="annual">Annual</option>
                        <option value="special">Special</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" class="form-control" placeholder="Optional description">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="add" class="btn btn-success"><i class="fas fa-plus"></i> Add Type</button>
            </div>
        </form>
    </div>
    
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> All Exam Types</h3>
    </div>
    <div class="card-body">
        <?php if (count($types) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $t): ?>
                <tr>
                    <td><strong><?php echo $t['exam_name']; ?></strong></td>
                    <td><?php echo ucfirst(str_replace('_',' ',$t['exam_type'])); ?></td>
                    <td>
                        <span class="badge <?php echo ($t['status'] == 'active') ? 'badge-success' : 'badge-secondary'; ?>">
                            <?php echo ucfirst($t['status']); ?>
                        </span>
                    </td>
                    <td><?php echo $t['description'] ?: '-'; ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                            <input type="hidden" name="exam_name" value="<?php echo htmlspecialchars($t['exam_name']); ?>">
                            <input type="hidden" name="exam_type" value="<?php echo $t['exam_type']; ?>">
                            <input type="hidden" name="description" value="<?php echo htmlspecialchars($t['description']); ?>">
                            <input type="hidden" name="status" value="<?php echo $t['status']; ?>">
                            <button type="submit" name="update" class="btn btn-sm btn-primary" title="Quick Save"><i class="fas fa-save"></i></button>
                        </form>
                        <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this type?');" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No exam types found. Add your first type above.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

