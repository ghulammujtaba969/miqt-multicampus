<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Exams - Class Wise';

$range = $_GET['range'] ?? 'monthly';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

function rngE($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=rngE($range,$from,$to);

// Summary by class across exams in range: average percentage and pass rate (derive if not stored)
$sql = "
SELECT c.id AS class_id, c.class_name,
       COUNT(DISTINCT e.id) AS exams_count,
       COUNT(er.id) AS results_count,
       AVG(
         CASE WHEN e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL
              THEN (er.obtained_marks / e.total_marks) * 100 ELSE NULL END
       ) AS avg_percentage,
       SUM(
         CASE WHEN e.passing_marks IS NOT NULL AND e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL AND er.obtained_marks >= e.passing_marks
              THEN 1 ELSE 0 END
       ) AS passes,
       SUM(CASE WHEN er.id IS NOT NULL THEN 1 ELSE 0 END) AS evaluated
FROM exams e
LEFT JOIN classes c ON c.id = e.class_id
LEFT JOIN exam_results er ON er.exam_id = e.id
LEFT JOIN students s ON s.id = er.student_id
WHERE e.exam_date BETWEEN ? AND ?
GROUP BY c.id, c.class_name
ORDER BY c.class_name";
$st = $db->prepare($sql);
$st->execute([$date_from,$date_to]);
$rows = $st->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-alt"></i> Exams - Class Wise</h1>
    <p class="subtitle">Exam outcomes per class</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group">
                <label>Range</label>
                <div>
                    <?php $mk=function($r){return '?range='.$r;}; ?>
                    <a class="btn <?php echo ($range=='daily')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('daily'); ?>">Daily</a>
                    <a class="btn <?php echo ($range=='weekly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('weekly'); ?>">Weekly</a>
                    <a class="btn <?php echo ($range=='monthly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('monthly'); ?>">Monthly</a>
                    <a class="btn <?php echo ($range=='yearly')?'btn-primary':'btn-secondary'; ?>" href="<?php echo $mk('yearly'); ?>">Yearly</a>
                </div>
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
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_exams_class_pdf.php?range=<?php echo urlencode($range); ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Exams</th>
                    <th>Results</th>
                    <th>Avg %</th>
                    <th>Pass Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): $evaluated=(int)$r['evaluated']; $passes=(int)$r['passes']; $rate= $evaluated>0? round(($passes/$evaluated)*100,2):0; ?>
                <tr>
                    <td><?php echo $r['class_name'] ?? 'All Classes'; ?></td>
                    <td><?php echo (int)$r['exams_count']; ?></td>
                    <td><?php echo (int)$r['results_count']; ?></td>
                    <td><?php echo ($r['avg_percentage']!==null)? round($r['avg_percentage'],2).'%' : 'N/A'; ?></td>
                    <td><?php echo $rate; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
</div>

<?php require_once '../../includes/footer.php'; ?>
