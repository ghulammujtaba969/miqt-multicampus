<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Exams - Student Wise';

$range = $_GET['range'] ?? 'monthly';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function erange($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=erange($range,$from,$to);

// Classes
$stmt=$db->prepare("SELECT id, class_name FROM classes WHERE status='active' ORDER BY class_name");
$stmt->execute();
$classes=$stmt->fetchAll();

$students=[];$rows=[];
if($class_id>0){
    $st=$db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
    $st->execute([$class_id]);
    $students=$st->fetchAll();

    $sql="SELECT s.id AS student_id,
                  AVG(CASE WHEN e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL THEN (er.obtained_marks/e.total_marks)*100 ELSE NULL END) AS avg_percentage,
                  SUM(CASE WHEN e.passing_marks IS NOT NULL AND e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL AND er.obtained_marks >= e.passing_marks THEN 1 ELSE 0 END) AS passes,
                  COUNT(er.id) AS attempts
           FROM students s
           LEFT JOIN exam_results er ON er.student_id = s.id
           LEFT JOIN exams e ON e.id = er.exam_id AND e.exam_date BETWEEN ? AND ?
           WHERE s.class_id = ?
           GROUP BY s.id";
    $st=$db->prepare($sql);
    $st->execute([$date_from,$date_to,$class_id]);
    while($r=$st->fetch()){ $rows[$r['student_id']]=$r; }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i> Exams - Student Wise</h1>
    <p class="subtitle">Per-student exam outcomes</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group">
                <label>Range</label>
                <div>
                    <?php $mk=function($r)use($class_id){return '?range='.$r.($class_id?('&class_id='.$class_id):'');}; ?>
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
                    <?php foreach($classes as $c): ?>
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
        <h3><i class="fas fa-list"></i> Summary (<?php echo date('d M Y',strtotime($date_from)); ?> - <?php echo date('d M Y',strtotime($date_to)); ?>)</h3>
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_exams_student_pdf.php?class_id=<?php echo (int)$class_id; ?>&range=<?php echo urlencode($range); ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
    <div class="card-body">
        <?php if($class_id>0 && count($students)>0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Attempts</th>
                    <th>Avg %</th>
                    <th>Passes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): $r=$rows[$s['id']]??['attempts'=>0,'avg_percentage'=>null,'passes'=>0]; ?>
                <tr>
                    <td><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong><br><small><?php echo $s['student_id']; ?></small></td>
                    <td><?php echo (int)$r['attempts']; ?></td>
                    <td><?php echo ($r['avg_percentage']!==null)? round($r['avg_percentage'],2).'%' : 'N/A'; ?></td>
                    <td><span class="badge badge-success"><?php echo (int)$r['passes']; ?></span></td>
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
