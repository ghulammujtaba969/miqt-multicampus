<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Progress - Class Wise';

$range = $_GET['range'] ?? 'daily';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function resolveRange($range,$from,$to){$t=date('Y-m-d');if($range=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($range=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($range=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($range=='custom'&&$from&&$to)return [$from,$to];return [$t,$t];}
[$date_from,$date_to]=resolveRange($range,$from,$to);

// Classes
$stmt=$db->prepare("SELECT id, class_name FROM classes WHERE status='active' ORDER BY class_name");
$stmt->execute();
$classes=$stmt->fetchAll();

// Summary by class: count of sabak/sabqi/manzil
$params=[$date_from,$date_to,$date_from,$date_to,$date_from,$date_to];
$whereClass='';
if($class_id>0){$whereClass=' AND s.class_id = ?';$params[]=$class_id;$params[]=$class_id;$params[]=$class_id;}

$sql="
SELECT c.id as class_id, c.class_name,
       COALESCE(sab.sabak_count,0) AS sabak,
       COALESCE(sbq.sabqi_count,0) AS sabqi,
       COALESCE(mz.manzil_count,0) AS manzil
FROM classes c
LEFT JOIN (
    SELECT s.class_id, COUNT(*) AS sabak_count
    FROM sabak_records r JOIN students s ON s.id=r.student_id
    WHERE r.record_date BETWEEN ? AND ? {$whereClass}
    GROUP BY s.class_id
) sab ON sab.class_id=c.id
LEFT JOIN (
    SELECT s.class_id, COUNT(*) AS sabqi_count
    FROM sabqi_records r JOIN students s ON s.id=r.student_id
    WHERE r.record_date BETWEEN ? AND ? {$whereClass}
    GROUP BY s.class_id
) sbq ON sbq.class_id=c.id
LEFT JOIN (
    SELECT s.class_id, COUNT(*) AS manzil_count
    FROM manzil_records r JOIN students s ON s.id=r.student_id
    WHERE r.record_date BETWEEN ? AND ? {$whereClass}
    GROUP BY s.class_id
) mz ON mz.class_id=c.id
WHERE c.status='active'";
$st=$db->prepare($sql);
$st->execute($params);
$rows=$st->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-book-quran"></i> Progress - Class Wise</h1>
    <p class="subtitle">Sabak, Sabqi, Manzil counts per class</p>
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
                <label for="class_id">Filter Class</label>
                <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                    <option value="0">All</option>
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
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Sabak</th>
                    <th>Sabqi</th>
                    <th>Manzil</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): $total=$r['sabak']+$r['sabqi']+$r['manzil']; ?>
                <tr>
                    <td><?php echo $r['class_name']; ?></td>
                    <td><span class="badge badge-info"><?php echo $r['sabak']; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $r['sabqi']; ?></span></td>
                    <td><span class="badge badge-success"><?php echo $r['manzil']; ?></span></td>
                    <td><?php echo $total; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php if ($class_id>0): ?>
        <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/reports/export_progress_class_docx.php?class_id=<?php echo (int)$class_id; ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-word"></i> Export Class DOCX
        </a>
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_progress_class_pdf.php?class_id=<?php echo (int)$class_id; ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-pdf"></i> Export Class PDF
        </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
