<?php
/**
 * Teacher Attendance
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Teacher Attendance';

// Get all active teachers
$sql = "SELECT * FROM teachers WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$teachers = $stmt->fetchAll();

// Get selected date
$selected_date = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : date('Y-m-d');

// Check if attendance already exists for this date
$existing_attendance = [];
if (!empty($selected_date)) {
    $sql = "SELECT teacher_id, status, check_in, check_out FROM teacher_attendance WHERE attendance_date = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$selected_date]);
    while ($row = $stmt->fetch()) {
        $existing_attendance[$row['teacher_id']] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $attendance_date = sanitize($_POST['attendance_date']);
    $attendance = $_POST['attendance'] ?? [];
    $check_in_times = $_POST['check_in_time'] ?? [];
    $check_out_times = $_POST['check_out_time'] ?? [];

    if (empty($attendance_date)) {
        $error = 'Please select a date';
    } else {
        try {
            $db->beginTransaction();

            foreach ($attendance as $teacher_id => $status) {
                $check_in = !empty($check_in_times[$teacher_id]) ? $check_in_times[$teacher_id] : null;
                $check_out = !empty($check_out_times[$teacher_id]) ? $check_out_times[$teacher_id] : null;

                // Check if attendance already exists
                $sql = "SELECT id FROM teacher_attendance WHERE teacher_id = ? AND attendance_date = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$teacher_id, $attendance_date]);

                if ($stmt->fetch()) {
                    // Update existing
                    $sql = "UPDATE teacher_attendance SET status = ?, check_in = ?, check_out = ?
                            WHERE teacher_id = ? AND attendance_date = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$status, $check_in, $check_out, $teacher_id, $attendance_date]);
                } else {
                    // Insert new
                    $sql = "INSERT INTO teacher_attendance (teacher_id, attendance_date, status, check_in, check_out)
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$teacher_id, $attendance_date, $status, $check_in, $check_out]);
                }
            }

            $db->commit();
            logActivity($db, 'Mark Attendance', 'Attendance', "Marked teacher attendance for $attendance_date");
            setFlash('success', 'Attendance marked successfully!');
            redirect(SITE_URL . '/modules/attendance/teacher_attendance.php');
        } catch(PDOException $e) {
            $db->rollBack();
            $error = 'Error marking attendance: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-check"></i> Teacher Attendance</h1>
    <p class="subtitle">Mark daily attendance for teachers</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row mb-20">
                <div class="form-group">
                    <label for="attendance_date">Attendance Date *</label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                           value="<?php echo $selected_date; ?>" required onchange="this.form.submit()">
                </div>
            </div>

            <?php if (count($teachers) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Check-In Time</th>
                            <th>Check-Out Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher):
                            $existing = $existing_attendance[$teacher['id']] ?? null;
                            $current_status = $existing['status'] ?? 'present';
                            $check_in = $existing['check_in'] ?? '';
                            $check_out = $existing['check_out'] ?? '';
                        ?>
                        <tr>
                            <td><?php echo $teacher['teacher_id']; ?></td>
                            <td>
                                <strong><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></strong><br>
                                <small class="text-muted"><?php echo $teacher['specialization'] ?? 'Teacher'; ?></small>
                            </td>
                            <td>
                                <select name="attendance[<?php echo $teacher['id']; ?>]" class="form-control" required>
                                    <option value="present" <?php echo ($current_status == 'present') ? 'selected' : ''; ?>>Present</option>
                                    <option value="absent" <?php echo ($current_status == 'absent') ? 'selected' : ''; ?>>Absent</option>
                                    <option value="late" <?php echo ($current_status == 'late') ? 'selected' : ''; ?>>Late</option>
                                    <option value="leave" <?php echo ($current_status == 'leave') ? 'selected' : ''; ?>>On Leave</option>
                                    <option value="half_day" <?php echo ($current_status == 'half_day') ? 'selected' : ''; ?>>Half Day</option>
                                </select>
                            </td>
                            <td>
                                <input type="time" name="check_in_time[<?php echo $teacher['id']; ?>]" class="form-control"
                                       value="<?php echo $check_in; ?>">
                            </td>
                            <td>
                                <input type="time" name="check_out_time[<?php echo $teacher['id']; ?>]" class="form-control"
                                       value="<?php echo $check_out; ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-group mt-20">
                <button type="submit" name="submit_attendance" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Attendance
                </button>
                <a href="view_attendance.php" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> View Reports
                </a>
            </div>

            <?php else: ?>
            <p class="text-center text-muted">No active teachers found</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
