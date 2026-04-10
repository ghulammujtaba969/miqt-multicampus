<?php
/**
 * Edit Class
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Class';

$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch class
$sql = "SELECT * FROM classes WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$class_id]);
$class = $stmt->fetch();

if (!$class) {
    setFlash('danger', 'Class not found');
    redirect(SITE_URL . '/modules/students/classes.php');
}

// Fetch teachers for dropdown
$sql = "SELECT id, teacher_id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$teachers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = sanitize($_POST['class_name']);
    $capacity = (int)sanitize($_POST['capacity']);
    $status = sanitize($_POST['status']);
    $class_teacher_id = !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : null;

    try {
        $sql = "UPDATE classes SET class_name = ?, capacity = ?, status = ?, class_teacher_id = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$class_name, $capacity, $status, $class_teacher_id, $class_id]);

        logActivity($db, 'Edit Class', 'Classes', "Updated class: $class_name (ID: $class_id)");
        setFlash('success', 'Class updated successfully!');
        redirect(SITE_URL . '/modules/students/classes.php');
    } catch (PDOException $e) {
        $error = 'Error updating class: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Class</h1>
    <p class="subtitle">Update class details and assignment</p>
    </div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="class_name">Class Name *</label>
                    <input type="text" name="class_name" id="class_name" class="form-control" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="capacity">Capacity *</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="<?php echo (int)$class['capacity']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($class['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($class['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="class_teacher_id">Assign Class Teacher</label>
                <select name="class_teacher_id" id="class_teacher_id" class="form-control">
                    <option value="">-- Not Assigned --</option>
                    <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo $teacher['id']; ?>" <?php echo ($class['class_teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                        <?php echo $teacher['teacher_id'] . ' - ' . $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Class
                </button>
                <a href="classes.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
 </div>

<?php require_once '../../includes/footer.php'; ?>

