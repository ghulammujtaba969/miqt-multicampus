<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Progress - Student Wise';

$range = $_GET['range'] ?? 'daily';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function rng($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=rng($range,$from,$to);

// Classes
$stmt=$db->prepare("SELECT id, class_name FROM classes WHERE status='active' ORDER BY class_name");
$stmt->execute();
$classes=$stmt->fetchAll();

$students=[];$summary=[];
if($class_id>0){
    $st=$db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
    $st->execute([$class_id]);
    $students=$st->fetchAll();

    $sqlSab="SELECT r.student_id, COUNT(*) c FROM sabak_records r WHERE r.record_date BETWEEN ? AND ? AND r.student_id IN (SELECT id FROM students WHERE class_id=?) GROUP BY r.student_id";
    $st=$db->prepare($sqlSab);$st->execute([$date_from,$date_to,$class_id]);while($row=$st->fetch()){ $summary[$row['student_id']]['sabak']=$row['c']; }
    $sqlSbq="SELECT r.student_id, COUNT(*) c FROM sabqi_records r WHERE r.record_date BETWEEN ? AND ? AND r.student_id IN (SELECT id FROM students WHERE class_id=?) GROUP BY r.student_id";
    $st=$db->prepare($sqlSbq);$st->execute([$date_from,$date_to,$class_id]);while($row=$st->fetch()){ $summary[$row['student_id']]['sabqi']=$row['c']; }
    $sqlMz="SELECT r.student_id, COUNT(*) c FROM manzil_records r WHERE r.record_date BETWEEN ? AND ? AND r.student_id IN (SELECT id FROM students WHERE class_id=?) GROUP BY r.student_id";
    $st=$db->prepare($sqlMz);$st->execute([$date_from,$date_to,$class_id]);while($row=$st->fetch()){ $summary[$row['student_id']]['manzil']=$row['c']; }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-graduate"></i> Progress - Student Wise</h1>
    <p class="subtitle">Per-student Sabak, Sabqi, Manzil counts</p>
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
    </div>
    <div class="card-body">
        <?php if($class_id>0 && count($students)>0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Sabak</th>
                    <th>Sabqi</th>
                    <th>Manzil</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s):
                    $sab=$summary[$s['id']]['sabak']??0; $sbq=$summary[$s['id']]['sabqi']??0; $mz=$summary[$s['id']]['manzil']??0; $tot=$sab+$sbq+$mz; ?>
                <tr>
                    <td><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong><br><small><?php echo $s['student_id']; ?></small></td>
                    <td><span class="badge badge-info"><?php echo $sab; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $sbq; ?></span></td>
                    <td><span class="badge badge-success"><?php echo $mz; ?></span></td>
                    <td><?php echo $tot; ?></td>
                    <td>
                        <a class="btn btn-sm btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_progress_student_pdf.php?student_id=<?php echo (int)$s['id']; ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>" title="Result Card PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php elseif ($class_id>0): ?>
        <p class="text-center text-muted">No students found for the selected class.</p>
        <?php else: ?>
        <p class="text-center text-muted">Please select a class.</p>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/reports/export_progress_class_docx.php?class_id=<?php echo (int)$class_id; ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-word"></i> Export Class DOCX
        </a>
        <?php if ($class_id>0): ?>
        <a class="btn btn-danger" href="<?php echo SITE_URL; ?>/modules/reports/export_progress_class_pdf.php?class_id=<?php echo (int)$class_id; ?>&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>">
            <i class="fas fa-file-pdf"></i> Export Class PDF
        </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
