<?php
/**
 * Manage Classes
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Classes';

// Handle Add Class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $class_name = sanitize($_POST['class_name']);
    $capacity = sanitize($_POST['capacity']);
    $class_teacher_id = !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : null;

    try {
        $sql = "INSERT INTO classes (class_name, capacity, class_teacher_id, status) VALUES (?, ?, ?, 'active')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$class_name, $capacity, $class_teacher_id]);

        logActivity($db, 'Add Class', 'Classes', "Added class: $class_name");
        setFlash('success', 'Class added successfully!');
        redirect(SITE_URL . '/modules/students/classes.php');
    } catch(PDOException $e) {
        $error = 'Error adding class: ' . $e->getMessage();
    }
}

// Handle Update Teacher Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_teacher'])) {
    $class_id = (int)$_POST['class_id'];
    $class_teacher_id = !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : null;

    try {
        $sql = "UPDATE classes SET class_teacher_id = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$class_teacher_id, $class_id]);

        logActivity($db, 'Update Class Teacher', 'Classes', "Updated teacher for class ID: $class_id");
        setFlash('success', 'Teacher assigned successfully!');
        redirect(SITE_URL . '/modules/students/classes.php');
    } catch(PDOException $e) {
        $error = 'Error updating teacher: ' . $e->getMessage();
    }
}

// Get all teachers for dropdown
$sql = "SELECT id, teacher_id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$teachers = $stmt->fetchAll();

// Get all classes
$sql = "SELECT c.*, COUNT(s.id) as student_count, t.first_name, t.last_name, t.teacher_id
        FROM classes c
        LEFT JOIN students s ON c.id = s.class_id AND s.status = 'active'
        LEFT JOIN teachers t ON c.class_teacher_id = t.id
        GROUP BY c.id
        ORDER BY c.class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-school"></i> Manage Classes</h1>
    <p class="subtitle">Add and manage student classes/groups</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Add New Class</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="class_name">Class Name *</label>
                    <input type="text" name="class_name" id="class_name" class="form-control"
                           placeholder="e.g., Class A, Hifz Group 1" required>
                </div>

                <div class="form-group">
                    <label for="capacity">Capacity *</label>
                    <input type="number" name="capacity" id="capacity" class="form-control"
                           value="30" min="1" required>
                </div>

                <div class="form-group">
                    <label for="class_teacher_id">Assign Teacher</label>
                    <select name="class_teacher_id" id="class_teacher_id" class="form-control">
                        <option value="">-- Select Teacher (Optional) --</option>
                        <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo $teacher['id']; ?>">
                            <?php echo $teacher['teacher_id'] . ' - ' . $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" name="add_class" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Class
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> All Classes</h3>
    </div>
    <div class="card-body">
        <?php if (count($classes) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Class Teacher</th>
                    <th>Capacity</th>
                    <th>Current Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                <tr>
                    <td><strong><?php echo $class['class_name']; ?></strong></td>
                    <td>
                        <form method="POST" action="" style="display: inline-flex; gap: 5px;">
                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                            <select name="class_teacher_id" class="form-control" style="width: 200px;">
                                <option value="">-- Not Assigned --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"
                                    <?php echo ($class['class_teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                                    <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_teacher" class="btn btn-sm btn-primary" title="Update Teacher">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </td>
                    <td><?php echo $class['capacity']; ?></td>
                    <td>
                        <span class="badge <?php echo ($class['student_count'] >= $class['capacity']) ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo $class['student_count']; ?> / <?php echo $class['capacity']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo ($class['status'] == 'active') ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo ucfirst($class['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="students.php?class_id=<?php echo $class['id']; ?>" class="btn btn-sm btn-info" title="View Students">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-primary" title="Edit Class">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No classes found. Add your first class above.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
