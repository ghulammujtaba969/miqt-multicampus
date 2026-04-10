<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Attendance - Student Wise';

$range = $_GET['range'] ?? 'daily';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function resolveDateRange2($range, $from, $to) {
    $today = date('Y-m-d');
    switch ($range) {
        case 'weekly': return [date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week'))];
        case 'monthly': return [date('Y-m-01'), date('Y-m-t')];
        case 'yearly': return [date('Y-01-01'), date('Y-12-31')];
        case 'custom': return ($from && $to) ? [$from, $to] : [$today, $today];
        default: return [$today, $today];
    }
}
[$date_from, $date_to] = resolveDateRange2($range, $from, $to);

// Classes
$stmt = $db->prepare("SELECT id, class_name FROM classes WHERE status='active' ORDER BY class_name");
$stmt->execute();
$classes = $stmt->fetchAll();

// Students in class
$students = [];
if ($class_id > 0) {
    $st = $db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id = ? AND status='active' ORDER BY first_name, last_name");
    $st->execute([$class_id]);
    $students = $st->fetchAll();
}

$summary = [];
if ($class_id > 0) {
    $sql = "SELECT sa.student_id,
                   SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
                   SUM(CASE WHEN sa.status='absent' THEN 1 ELSE 0 END) AS absent,
                   SUM(CASE WHEN sa.status='leave' THEN 1 ELSE 0 END) AS `leave`,
                   COUNT(*) AS total
            FROM student_attendance sa
            WHERE sa.attendance_date BETWEEN ? AND ? AND sa.class_id = ?
            GROUP BY sa.student_id";
    $st = $db->prepare($sql);
    $st->execute([$date_from, $date_to, $class_id]);
    while ($row = $st->fetch()) { $summary[$row['student_id']] = $row; }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-check"></i> Attendance - Student Wise</h1>
    <p class="subtitle">View attendance summary per student</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group">
                <label>Range</label>
                <div>
                    <?php $mk = function($r) use($class_id){ return '?range='.$r.($class_id?('&class_id='.$class_id):''); }; ?>
                    <a class="btn <?php echo ($range=='daily')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('daily'); ?>">Daily</a>
                    <a class="btn <?php echo ($range=='weekly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('weekly'); ?>">Weekly</a>
                    <a class="btn <?php echo ($range=='monthly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('monthly'); ?>">Monthly</a>
                    <a class="btn <?php echo ($range=='yearly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('yearly'); ?>">Yearly</a>
                </div>
            </div>
            <div class="form-group">
                <label for="class_id">Class</label>
                <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                    <option value="0">Select Class</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($class_id==$c['id'])?'selected':''; ?>><?php echo $c['class_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Custom</label>
                <div style="display:flex;gap:6px;">
                    <input type="hidden" name="range" value="custom">
                    <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                    <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Apply</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Summary (<?php echo date('d M Y', strtotime($date_from)); ?> - <?php echo date('d M Y', strtotime($date_to)); ?>)</h3>
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_attendance_pdf.php?range=<?php echo urlencode($range); ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>&class_id=<?php echo (int)$class_id; ?>&filename=<?php echo urlencode(date('d F Y', strtotime($date_to)).'.pdf'); ?>">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
    <div class="card-body">
        <?php if ($class_id>0 && count($students)>0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Leave</th>
                    <th>Total</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): 
                    $sum = $summary[$s['id']] ?? ['present'=>0,'absent'=>0,'leave'=>0,'total'=>0];
                    $pct = $sum['total']>0 ? round(($sum['present']/$sum['total'])*100,2) : 0;
                ?>
                <tr>
                    <td><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong><br><small><?php echo $s['student_id']; ?></small></td>
                    <td><span class="badge badge-success"><?php echo $sum['present']; ?></span></td>
                    <td><span class="badge badge-danger"><?php echo $sum['absent']; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $sum['leave']; ?></span></td>
                    <td><?php echo $sum['total']; ?></td>
                    <td><?php echo $pct; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php elseif ($class_id>0): ?>
        <p class="text-center text-muted">No students found for selected class.</p>
        <?php else: ?>
        <p class="text-center text-muted">Please select a class.</p>
        <?php endif; ?>
    </div>
    
</div>

<?php require_once '../../includes/footer.php'; ?>
