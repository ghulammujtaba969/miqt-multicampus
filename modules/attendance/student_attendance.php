<?php
/**
 * Mark Student Attendance
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Student Attendance';

// Get selected date and class
$attendance_date = isset($_POST['attendance_date']) ? sanitize($_POST['attendance_date']) : date('Y-m-d');
$class_id = isset($_POST['class_id']) ? sanitize($_POST['class_id']) : '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $attendance = $_POST['attendance'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    try {
        $db->beginTransaction();

        foreach ($attendance as $student_id => $status) {
            // Check if attendance already exists
            $sql = "SELECT id FROM student_attendance WHERE student_id = ? AND attendance_date = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$student_id, $attendance_date]);

            if ($stmt->fetch()) {
                // Update existing
                $sql = "UPDATE student_attendance SET status = ?, remarks = ?, marked_by = ?
                        WHERE student_id = ? AND attendance_date = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$status, $remarks[$student_id] ?? '', getUserId(), $student_id, $attendance_date]);
            } else {
                // Insert new
                $sql = "INSERT INTO student_attendance (student_id, class_id, attendance_date, status, remarks, marked_by)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$student_id, $class_id, $attendance_date, $status, $remarks[$student_id] ?? '', getUserId()]);
            }
        }

        $db->commit();
        logActivity($db, 'Mark Attendance', 'Attendance', "Marked attendance for " . count($attendance) . " students");
        setFlash('success', 'Attendance marked successfully!');
    } catch(PDOException $e) {
        $db->rollback();
        $error = 'Error marking attendance: ' . $e->getMessage();
    }
}

// Get all active classes
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get students if class selected
$students = [];
if (!empty($class_id)) {
    $sql = "SELECT s.*,
            (SELECT status FROM student_attendance
             WHERE student_id = s.id AND attendance_date = ?) as today_status
            FROM students s
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$attendance_date, $class_id]);
    $students = $stmt->fetchAll();
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-check"></i> Mark Student Attendance</h1>
    <p class="subtitle">Select class and date to mark attendance</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="class_id">Select Class *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Choose Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo $class['class_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="attendance_date">Date *</label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                           value="<?php echo $attendance_date; ?>" required>
                </div>

                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (count($students) > 0): ?>
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Students Attendance - <?php echo formatDate($attendance_date); ?></h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="attendance_date" value="<?php echo $attendance_date; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                        <td>
                            <select name="attendance[<?php echo $student['id']; ?>]" class="form-control" required>
                                
                                <option value="present" <?php echo ($student['today_status'] == 'present') ? 'selected' : ''; ?>>
                                    ✅ Present
                                </option>
                                <option value="absent" <?php echo ($student['today_status'] == 'absent') ? 'selected' : ''; ?>>
                                    ❌ Absent
                                </option>
                                <option value="leave" <?php echo ($student['today_status'] == 'leave') ? 'selected' : ''; ?>>
                                    📝 Leave
                                </option>
                                <option value="late" <?php echo ($student['today_status'] == 'late') ? 'selected' : ''; ?>>
                                    ⏰ Late
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="remarks[<?php echo $student['id']; ?>]" class="form-control"
                                   placeholder="Optional remarks...">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="form-group">
                <button type="submit" name="mark_attendance" class="btn btn-success btn-block">
                    <i class="fas fa-check"></i> Save Attendance
                </button>
            </div>
        </form>
    </div>
</div>
<?php elseif (!empty($class_id)): ?>
<div class="alert alert-warning">No students found in the selected class.</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
