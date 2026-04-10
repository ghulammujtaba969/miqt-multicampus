<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Attendance - Class Wise';

// Filters
$range = $_GET['range'] ?? 'daily';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function resolveDateRange($range, $from, $to) {
    $today = date('Y-m-d');
    switch ($range) {
        case 'weekly':
            $monday = date('Y-m-d', strtotime('monday this week'));
            $sunday = date('Y-m-d', strtotime('sunday this week'));
            return [$monday, $sunday];
        case 'monthly':
            return [date('Y-m-01'), date('Y-m-t')];
        case 'yearly':
            return [date('Y-01-01'), date('Y-12-31')];
        case 'custom':
            if ($from && $to) return [$from, $to];
            return [$today, $today];
        case 'daily':
        default:
            return [$today, $today];
    }
}
[$date_from, $date_to] = resolveDateRange($range, $from, $to);

// Dropdown classes
$stmt = $db->prepare("SELECT id, class_name FROM classes WHERE status='active' ORDER BY class_name");
$stmt->execute();
$classes = $stmt->fetchAll();

// Query summary by class
$params = [$date_from, $date_to];
$whereClass = '';
if ($class_id > 0) { $whereClass = ' AND sa.class_id = ?'; $params[] = $class_id; }

$sql = "
SELECT c.id as class_id, c.class_name,
       SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
       SUM(CASE WHEN sa.status='absent' THEN 1 ELSE 0 END) AS absent,
       SUM(CASE WHEN sa.status='leave' THEN 1 ELSE 0 END) AS `leave`,
       COUNT(*) AS total
FROM student_attendance sa
JOIN classes c ON c.id = sa.class_id
WHERE sa.attendance_date BETWEEN ? AND ? {$whereClass}
GROUP BY c.id, c.class_name
ORDER BY c.class_name";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-clipboard-check"></i> Attendance - Class Wise</h1>
    <p class="subtitle">View attendance summary per class</p>
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
                    <option value="0">All</option>
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
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_attendance_daily_class_pdf.php?range=<?php echo urlencode($range); ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
    <div class="card-body">
        <?php if (count($rows)>0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Leave</th>
                    <th>Total</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): 
                    $pct = $r['total']>0 ? round(($r['present']/$r['total'])*100,2) : 0;
                ?>
                <tr>
                    <td><?php echo $r['class_name']; ?></td>
                    <td><span class="badge badge-success"><?php echo $r['present']; ?></span></td>
                    <td><span class="badge badge-danger"><?php echo $r['absent']; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $r['leave']; ?></span></td>
                    <td><?php echo $r['total']; ?></td>
                    <td><?php echo $pct; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No attendance records found for the selected range.</p>
        <?php endif; ?>
    </div>
    
</div>

<?php require_once '../../includes/footer.php'; ?>
