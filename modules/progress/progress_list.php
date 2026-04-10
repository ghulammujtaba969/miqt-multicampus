<?php
/**
 * Daily Progress Records List
 * Lists saved Sabak/Sabqi/Manzil entries by Date and Class
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Daily Progress Records';

// Filters (GET)
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d');
$date_to   = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$class_id  = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$teacher_id = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 0;

// Dropdown data
$stmt = $db->prepare("SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name");
$stmt->execute();
$classes = $stmt->fetchAll();

$stmt = $db->prepare("SELECT id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY first_name, last_name");
$stmt->execute();
$teachers = $stmt->fetchAll();

// Build dynamic WHERE pieces
$params = [];
$whereDate = " AND r.record_date BETWEEN ? AND ? ";
// We will repeat params per UNION branch later

$whereClass = '';
if ($class_id > 0) { $whereClass = " AND s.class_id = ? "; }

$whereTeacher = '';
if ($teacher_id > 0) { $whereTeacher = " AND r.teacher_id = ? "; }

// Create unified keys of (record_date, class_id)
$sqlKeys = "
    SELECT r.record_date, s.class_id
    FROM sabak_records r
    JOIN students s ON s.id = r.student_id
    WHERE 1=1 {$whereDate} {$whereClass} {$whereTeacher}
    UNION
    SELECT r.record_date, s.class_id
    FROM sabqi_records r
    JOIN students s ON s.id = r.student_id
    WHERE 1=1 {$whereDate} {$whereClass} {$whereTeacher}
    UNION
    SELECT r.record_date, s.class_id
    FROM manzil_records r
    JOIN students s ON s.id = r.student_id
    WHERE 1=1 {$whereDate} {$whereClass} {$whereTeacher}
";
$stmt = $db->prepare($sqlKeys);
// Build params for each UNION branch (3 branches)
$paramsKeys = [];
for ($i = 0; $i < 3; $i++) {
    // date range
    $paramsKeys[] = $date_from; $paramsKeys[] = $date_to;
    // optional class
    if ($class_id > 0) { $paramsKeys[] = $class_id; }
    // optional teacher
    if ($teacher_id > 0) { $paramsKeys[] = $teacher_id; }
}
$stmt->execute($paramsKeys);
$keys = $stmt->fetchAll();

// Deduplicate keys in PHP (PDO with UNION may already dedup, but ensure)
$unique = [];
foreach ($keys as $k) {
    $key = $k['record_date'] . '|' . $k['class_id'];
    $unique[$key] = ['record_date' => $k['record_date'], 'class_id' => $k['class_id']];
}
$records = array_values($unique);

// Preload class names
$classNames = [];
foreach ($classes as $c) { $classNames[$c['id']] = $c['class_name']; }

// For each key, compute counts and teachers
function fetchCount($db, $table, $date, $classId, $teacherId = 0) {
    $sql = "SELECT COUNT(*) AS c
            FROM {$table} r
            JOIN students s ON s.id = r.student_id
            WHERE r.record_date = ? AND s.class_id = ?";
    $params = [$date, $classId];
    if ($teacherId > 0) { $sql .= " AND r.teacher_id = ?"; $params[] = $teacherId; }
    $st = $db->prepare($sql);
    $st->execute($params);
    return (int)$st->fetch()['c'];
}

function fetchTeachers($db, $date, $classId, $teacherId = 0) {
    $params = [$date, $classId, $date, $classId, $date, $classId];
    $teacherFilter = '';
    if ($teacherId > 0) {
        $teacherFilter = ' WHERE tt.teacher_id = ? ';
        $params[] = $teacherId;
    }
    $sql = "
        SELECT GROUP_CONCAT(DISTINCT CONCAT(t.first_name,' ',t.last_name) SEPARATOR ', ') AS names FROM (
            SELECT r.teacher_id FROM sabak_records r JOIN students s ON s.id=r.student_id
            WHERE r.record_date=? AND s.class_id=?
            UNION
            SELECT r.teacher_id FROM sabqi_records r JOIN students s ON s.id=r.student_id
            WHERE r.record_date=? AND s.class_id=?
            UNION
            SELECT r.teacher_id FROM manzil_records r JOIN students s ON s.id=r.student_id
            WHERE r.record_date=? AND s.class_id=?
        ) tt
        {$teacherFilter}
        JOIN teachers t ON t.id = tt.teacher_id
    ";
    $st = $db->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row && $row['names'] ? $row['names'] : '—';
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-list"></i> Daily Progress Records</h1>
    <p class="subtitle">Browse saved Sabak, Sabqi, and Manzil entries</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-arrow-left"></i> Back to Entry</a></p>
    <p><a class="btn btn-info" href="<?php echo SITE_URL; ?>/modules/progress/view_progress.php"><i class="fas fa-eye"></i> Open Read-only Viewer</a></p>
    
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filter</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="date_from">From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="form-group">
                    <label for="date_to">To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                <div class="form-group">
                    <label for="class_id">Class</label>
                    <select name="class_id" id="class_id" class="form-control">
                        <option value="0">All Classes</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($class_id == $c['id']) ? 'selected' : ''; ?>><?php echo $c['class_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="teacher_id">Teacher</label>
                    <select name="teacher_id" id="teacher_id" class="form-control">
                        <option value="0">All Teachers</option>
                        <?php foreach ($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php echo ($teacher_id == $t['id']) ? 'selected' : ''; ?>><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="align-self: end;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Apply</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-database"></i> Results</h3>
    </div>
    <div class="card-body">
        <?php if (count($records) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Class</th>
                    <th>Teacher(s)</th>
                    <th>Sabak</th>
                    <th>Sabqi</th>
                    <th>Manzil</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                <?php
                    $d = $r['record_date'];
                    $cid = (int)$r['class_id'];
                    $sabak = fetchCount($db, 'sabak_records', $d, $cid, $teacher_id);
                    $sabqi = fetchCount($db, 'sabqi_records', $d, $cid, $teacher_id);
                    $manzil = fetchCount($db, 'manzil_records', $d, $cid, $teacher_id);
                    $tnames = fetchTeachers($db, $d, $cid, $teacher_id);
                    $viewUrl = SITE_URL . '/modules/progress/view_progress.php?date=' . urlencode($d) . '&class_id=' . $cid;
                    if ($teacher_id > 0) { $viewUrl .= '&teacher_id=' . $teacher_id; }
                ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($d)); ?></td>
                    <td><?php echo htmlspecialchars($classNames[$cid] ?? ('Class #' . $cid)); ?></td>
                    <td><?php echo htmlspecialchars($tnames); ?></td>
                    <td><span class="badge badge-info"><?php echo $sabak; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $sabqi; ?></span></td>
                    <td><span class="badge badge-success"><?php echo $manzil; ?></span></td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="<?php echo $viewUrl; ?>" title="View"><i class="fas fa-eye"></i></a>
                        <a class="btn btn-sm btn-info" href="<?php echo $viewUrl; ?>&print=1" title="Print" target="_blank"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No records found for the selected filters.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
