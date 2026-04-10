<?php
/**
 * Manage Exams
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Exams';

// Handle exam creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_exam'])) {
    $exam_title = sanitize($_POST['exam_title']);
    $exam_type_id = sanitize($_POST['exam_type_id']);
    $exam_date = sanitize($_POST['exam_date']);
    $class_id = sanitize($_POST['class_id']);
    $total_marks = (isset($_POST['total_marks']) && $_POST['total_marks'] !== '') ? (int)sanitize($_POST['total_marks']) : null;
    $passing_marks = (isset($_POST['passing_marks']) && $_POST['passing_marks'] !== '') ? (int)sanitize($_POST['passing_marks']) : null;

    try {
        $sql = "INSERT INTO exams (exam_title, exam_type_id, exam_date, class_id, total_marks, passing_marks, status)
                VALUES (?, ?, ?, ?, ?, ?, 'scheduled')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam_title, $exam_type_id, $exam_date, $class_id, $total_marks, $passing_marks]);

        logActivity($db, 'Create Exam', 'Exams', "Created exam: $exam_title");
        setFlash('success', 'Exam created successfully!');
        redirect(SITE_URL . '/modules/exams/manage_exams.php');
    } catch(PDOException $e) {
        $error = 'Error creating exam: ' . $e->getMessage();
    }
}

// Handle exam deletion
if (isset($_GET['delete'])) {
    $exam_id = (int)$_GET['delete'];

    try {
        $sql = "DELETE FROM exams WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam_id]);

        logActivity($db, 'Delete Exam', 'Exams', "Deleted exam ID: $exam_id");
        setFlash('success', 'Exam deleted successfully!');
        redirect(SITE_URL . '/modules/exams/manage_exams.php');
    } catch(PDOException $e) {
        setFlash('danger', 'Error deleting exam: ' . $e->getMessage());
        redirect(SITE_URL . '/modules/exams/manage_exams.php');
    }
}

// Get all exams
$sql = "SELECT e.*, c.class_name, et.exam_name as exam_type_name FROM exams e
        LEFT JOIN classes c ON e.class_id = c.id
        LEFT JOIN exam_types et ON e.exam_type_id = et.id
        ORDER BY e.exam_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$exams = $stmt->fetchAll();

// Get classes for dropdown
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get exam types for dropdown
$sql = "SELECT * FROM exam_types WHERE status = 'active' ORDER BY exam_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$exam_types = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-alt"></i> Manage Exams</h1>
    <p class="subtitle">Create and manage examination schedules</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/exams/manage_exam_types.php"><i class="fas fa-tags"></i> Manage Exam Types</a></p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Create Exam Form -->
<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Create New Exam</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="exam_title">Exam Name *</label>
                    <input type="text" name="exam_title" id="exam_title" class="form-control"
                           placeholder="e.g., Mid Term Examination" required>
                </div>

                <div class="form-group">
                    <label for="exam_type_id">Exam Type *</label>
                    <select name="exam_type_id" id="exam_type_id" class="form-control" required>
                        <option value="">Select Type</option>
                        <?php foreach ($exam_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>"><?php echo $type['exam_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="exam_date">Exam Date *</label>
                    <input type="date" name="exam_date" id="exam_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="class_id">Class *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo $class['class_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_marks">Total Marks (optional)</label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control"
                           placeholder="e.g., 100">
                </div>

                <div class="form-group">
                    <label for="passing_marks">Passing Marks (optional)</label>
                    <input type="number" name="passing_marks" id="passing_marks" class="form-control"
                           placeholder="e.g., 40">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="create_exam" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Exam
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Exams List -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> All Exams</h3>
    </div>
    <div class="card-body">
        <?php if (count($exams) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Type</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Total Marks</th>
                        <th>Passing Marks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><strong><?php echo $exam['exam_title']; ?></strong></td>
                        <td><?php echo $exam['exam_type_name'] ?? 'N/A'; ?></td>
                        <td><?php echo $exam['class_name'] ?? 'All Classes'; ?></td>
                        <td><?php echo formatDate($exam['exam_date']); ?></td>
                        <td><?php echo ($exam['total_marks'] === null || $exam['total_marks'] === '') ? '-' : $exam['total_marks']; ?></td>
                        <td><?php echo ($exam['passing_marks'] === null || $exam['passing_marks'] === '') ? '-' : $exam['passing_marks']; ?></td>
                        <td>
                            <?php
                            $badges = [
                                'scheduled' => 'badge-info',
                                'ongoing' => 'badge-warning',
                                'completed' => 'badge-success',
                                'cancelled' => 'badge-danger'
                            ];
                            $badge = $badges[$exam['status']] ?? 'badge-secondary';
                            ?>
                            <span class="badge <?php echo $badge; ?>">
                                <?php echo ucfirst($exam['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="add_results.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-primary" title="Add Results">
                                <i class="fas fa-plus"></i>
                            </a>
                            <a href="view_results.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-sm btn-info" title="View Results">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="?delete=<?php echo $exam['id']; ?>" class="btn btn-sm btn-danger"
                               title="Delete" onclick="return confirm('Are you sure you want to delete this exam?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No exams created yet</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
