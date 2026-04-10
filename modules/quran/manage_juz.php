<?php
/**
 * Manage Quran Juz/Parts
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Quran Juz';

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $juz_number = (int)($_POST['juz_number'] ?? 0);
    $juz_name_arabic = sanitize($_POST['juz_name_arabic'] ?? '');
    $juz_name_english = sanitize($_POST['juz_name_english'] ?? '');
    $start_surah = sanitize($_POST['start_surah'] ?? '');
    $start_ayah = (int)($_POST['start_ayah'] ?? 0);
    $end_surah = sanitize($_POST['end_surah'] ?? '');
    $end_ayah = (int)($_POST['end_ayah'] ?? 0);
    $number_of_lines = (int)($_POST['number_of_lines'] ?? 0);
    $number_of_lines = $number_of_lines > 0 ? $number_of_lines : null;

    if (isset($_POST['add'])) {
        try {
            $sql = "INSERT INTO juz_reference (juz_number, juz_name_arabic, juz_name_english, start_surah, start_ayah, end_surah, end_ayah, number_of_lines) VALUES (?,?,?,?,?,?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$juz_number, $juz_name_arabic, $juz_name_english, $start_surah, $start_ayah, $end_surah, $end_ayah, $number_of_lines]);
            setFlash('success', 'Juz added successfully');
            redirect(SITE_URL . '/modules/quran/manage_juz.php');
        } catch (PDOException $e) {
            $error = 'Error adding juz: ' . $e->getMessage();
        }
    }

    if (isset($_POST['update'])) {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $sql = "UPDATE juz_reference SET juz_number = ?, juz_name_arabic = ?, juz_name_english = ?, start_surah = ?, start_ayah = ?, end_surah = ?, end_ayah = ?, number_of_lines = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$juz_number, $juz_name_arabic, $juz_name_english, $start_surah, $start_ayah, $end_surah, $end_ayah, $number_of_lines, $id]);
            setFlash('success', 'Juz updated successfully');
            redirect(SITE_URL . '/modules/quran/manage_juz.php');
        } catch (PDOException $e) {
            $error = 'Error updating juz: ' . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = $db->prepare("DELETE FROM juz_reference WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Juz deleted');
        redirect(SITE_URL . '/modules/quran/manage_juz.php');
    } catch (PDOException $e) {
        $error = 'Error deleting juz: ' . $e->getMessage();
    }
}

// Edit state
$editJuz = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM juz_reference WHERE id = ?");
    $stmt->execute([$id]);
    $editJuz = $stmt->fetch();
}

// List all juz
$stmt = $db->query("SELECT * FROM juz_reference ORDER BY juz_number");
$juzList = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-book-quran"></i> Manage Quran Juz</h1>
    <p class="subtitle">Add, edit and delete Juz/Parts</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-arrow-left"></i> Back to Progress</a></p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><?php echo $editJuz ? 'Edit Juz' : 'Add New Juz'; ?></h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php if ($editJuz): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editJuz['id']; ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="juz_number">Juz Number *</label>
                    <input type="number" min="1" max="30" name="juz_number" id="juz_number" class="form-control" value="<?php echo ($editJuz['juz_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="number_of_lines">Number of Lines</label>
                    <input type="number" min="0" name="number_of_lines" id="number_of_lines" class="form-control" value="<?php echo ($editJuz['number_of_lines'] ?? ''); ?>" placeholder="Optional">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="juz_name_arabic">Juz Name (Arabic)</label>
                    <input type="text" name="juz_name_arabic" id="juz_name_arabic" class="form-control" value="<?php echo ($editJuz['juz_name_arabic'] ?? ''); ?>" placeholder="e.g. الٓمٓ">
                </div>
                <div class="form-group">
                    <label for="juz_name_english">Juz Name (English)</label>
                    <input type="text" name="juz_name_english" id="juz_name_english" class="form-control" value="<?php echo ($editJuz['juz_name_english'] ?? ''); ?>" placeholder="e.g. Alif Lam Meem">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="start_surah">Start Surah</label>
                    <input type="text" name="start_surah" id="start_surah" class="form-control" value="<?php echo ($editJuz['start_surah'] ?? ''); ?>" placeholder="e.g. Al-Fatiha">
                </div>
                <div class="form-group">
                    <label for="start_ayah">Start Ayah</label>
                    <input type="number" min="1" name="start_ayah" id="start_ayah" class="form-control" value="<?php echo ($editJuz['start_ayah'] ?? ''); ?>" placeholder="e.g. 1">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="end_surah">End Surah</label>
                    <input type="text" name="end_surah" id="end_surah" class="form-control" value="<?php echo ($editJuz['end_surah'] ?? ''); ?>" placeholder="e.g. Al-Baqarah">
                </div>
                <div class="form-group">
                    <label for="end_ayah">End Ayah</label>
                    <input type="number" min="1" name="end_ayah" id="end_ayah" class="form-control" value="<?php echo ($editJuz['end_ayah'] ?? ''); ?>" placeholder="e.g. 141">
                </div>
            </div>
            <div class="form-group">
                <?php if ($editJuz): ?>
                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="manage_juz.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <?php else: ?>
                <button type="submit" name="add" class="btn btn-success"><i class="fas fa-plus"></i> Add Juz</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header"><h3><i class="fas fa-list"></i> All Juz</h3></div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name (Arabic)</th>
                    <th>Name (English)</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Lines</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($juzList as $j): ?>
                <tr>
                    <td><?php echo (int)$j['juz_number']; ?></td>
                    <td><?php echo ($j['juz_name_arabic']); ?></td>
                    <td><?php echo ($j['juz_name_english']); ?></td>
                    <td><?php echo ($j['start_surah']) . ':' . (int)$j['start_ayah']; ?></td>
                    <td><?php echo ($j['end_surah']) . ':' . (int)$j['end_ayah']; ?></td>
                    <td><?php echo $j['number_of_lines'] ? (int)$j['number_of_lines'] : '-'; ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="manage_juz.php?edit=<?php echo (int)$j['id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Delete this Juz?');">
                            <input type="hidden" name="id" value="<?php echo (int)$j['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

