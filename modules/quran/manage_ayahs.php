<?php
/**
 * Manage Quran Ayahs
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Ayahs';

// Ensure tables exist
try { $db->query("SELECT 1 FROM quran_surahs LIMIT 1"); } catch (PDOException $e) {
    $db->exec("CREATE TABLE IF NOT EXISTS quran_surahs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        surah_number INT NOT NULL,
        surah_name_ar VARCHAR(100) DEFAULT NULL,
        surah_name_en VARCHAR(100) NOT NULL,
        ayah_count INT DEFAULT NULL,
        UNIQUE KEY surah_number (surah_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}
try { $db->query("SELECT 1 FROM quran_ayahs LIMIT 1"); } catch (PDOException $e) {
    $db->exec("CREATE TABLE IF NOT EXISTS quran_ayahs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        surah_id INT NOT NULL,
        ayah_number INT NOT NULL,
        text_ar TEXT DEFAULT NULL,
        page_number INT DEFAULT NULL,
        UNIQUE KEY surah_ayah_unique (surah_id, ayah_number),
        KEY surah_id (surah_id),
        CONSTRAINT quran_ayahs_ibfk_1 FOREIGN KEY (surah_id) REFERENCES quran_surahs(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

// Load surahs for filter
$surahs = $db->query("SELECT id, surah_number, surah_name_en FROM quran_surahs ORDER BY surah_number")->fetchAll();

$selected_surah = isset($_GET['surah']) ? (int)$_GET['surah'] : (count($surahs) ? (int)$surahs[0]['id'] : 0);

// Handle Add/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surah_id = (int)($_POST['surah_id'] ?? $selected_surah);

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM quran_ayahs WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Ayah deleted');
            redirect(SITE_URL . '/modules/quran/manage_ayahs.php?surah=' . $surah_id);
        } catch (PDOException $e) {
            $error = 'Error deleting ayah: ' . $e->getMessage();
        }
    } else {
        $ayah_number = (int)($_POST['ayah_number'] ?? 0);
        $text_ar = trim($_POST['text_ar'] ?? '');
        $page_number = isset($_POST['page_number']) && $_POST['page_number'] !== '' ? (int)$_POST['page_number'] : null;

        if (isset($_POST['add'])) {
            try {
                $stmt = $db->prepare("INSERT INTO quran_ayahs (surah_id, ayah_number, text_ar, page_number) VALUES (?,?,?,?)");
                $stmt->execute([$surah_id, $ayah_number, $text_ar ?: null, $page_number]);
                setFlash('success', 'Ayah added');
                redirect(SITE_URL . '/modules/quran/manage_ayahs.php?surah=' . $surah_id);
            } catch (PDOException $e) {
                $error = 'Error adding ayah: ' . $e->getMessage();
            }
        }

        if (isset($_POST['update'])) {
            $id = (int)$_POST['id'];
            try {
                $stmt = $db->prepare("UPDATE quran_ayahs SET surah_id = ?, ayah_number = ?, text_ar = ?, page_number = ? WHERE id = ?");
                $stmt->execute([$surah_id, $ayah_number, $text_ar ?: null, $page_number, $id]);
                setFlash('success', 'Ayah updated');
                redirect(SITE_URL . '/modules/quran/manage_ayahs.php?surah=' . $surah_id);
            } catch (PDOException $e) {
                $error = 'Error updating ayah: ' . $e->getMessage();
            }
        }
    }
}

// Edit state
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM quran_ayahs WHERE id = ?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
    if ($edit) { $selected_surah = (int)$edit['surah_id']; }
}

// Fetch ayahs for selected surah
$ayahs = [];
if ($selected_surah) {
    $stmt = $db->prepare("SELECT * FROM quran_ayahs WHERE surah_id = ? ORDER BY ayah_number");
    $stmt->execute([$selected_surah]);
    $ayahs = $stmt->fetchAll();
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-align-justify"></i> Manage Ayahs</h1>
    <p class="subtitle">Add, edit and delete Ayahs of a Surah</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-arrow-left"></i> Back to Progress</a></p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-header"><h3><i class="fas fa-filter"></i> Select Surah</h3></div>
    <div class="card-body">
        <form method="GET" class="form-row">
            <div class="form-group">
                <select name="surah" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($surahs as $s): ?>
                        <option value="<?php echo (int)$s['id']; ?>" <?php echo ($selected_surah == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo (int)$s['surah_number'] . '. ' . htmlspecialchars($s['surah_name_en']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card mb-20">
    <div class="card-header"><h3><?php echo $edit ? 'Edit Ayah' : 'Add New Ayah'; ?></h3></div>
    <div class="card-body">
        <form method="POST">
            <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?php echo (int)$edit['id']; ?>">
            <?php endif; ?>
            <input type="hidden" name="surah_id" value="<?php echo (int)$selected_surah; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="ayah_number">Ayah Number *</label>
                    <input type="number" min="1" name="ayah_number" id="ayah_number" class="form-control" value="<?php echo htmlspecialchars($edit['ayah_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="page_number">Page Number</label>
                    <input type="number" min="1" name="page_number" id="page_number" class="form-control" value="<?php echo htmlspecialchars($edit['page_number'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="text_ar">Text (Arabic) - Optional</label>
                <textarea name="text_ar" id="text_ar" class="form-control" rows="3"><?php echo htmlspecialchars($edit['text_ar'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <?php if ($edit): ?>
                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="manage_ayahs.php?surah=<?php echo (int)$selected_surah; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <?php else: ?>
                <button type="submit" name="add" class="btn btn-success"><i class="fas fa-plus"></i> Add Ayah</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header"><h3><i class="fas fa-list"></i> Ayahs</h3></div>
    <div class="card-body">
        <?php if ($selected_surah && count($ayahs) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Page</th>
                    <th>Text (AR)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ayahs as $a): ?>
                <tr>
                    <td><?php echo (int)$a['ayah_number']; ?></td>
                    <td><?php echo htmlspecialchars($a['page_number']); ?></td>
                    <td style="max-width:600px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($a['text_ar']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="manage_ayahs.php?edit=<?php echo (int)$a['id']; ?>&surah=<?php echo (int)$selected_surah; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Delete this Ayah?');">
                            <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                            <input type="hidden" name="surah_id" value="<?php echo (int)$selected_surah; ?>">
                            <button type="submit" name="delete" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted">No ayahs found for this surah. Add the first one above.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

