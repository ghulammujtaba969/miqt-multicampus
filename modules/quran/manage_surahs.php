<?php
/**
 * Manage Quran Surahs
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Surahs';

// Ensure table exists (safe-guard)
try {
    $db->query("SELECT 1 FROM quran_surahs LIMIT 1");
} catch (PDOException $e) {
    // Create minimal table
    $db->exec("CREATE TABLE IF NOT EXISTS quran_surahs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        surah_number INT NOT NULL,
        surah_name_ar VARCHAR(100) DEFAULT NULL,
        surah_name_en VARCHAR(100) NOT NULL,
        ayah_count INT DEFAULT NULL,
        UNIQUE KEY surah_number (surah_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

// Auto-seed default 114 Surahs if table is empty
try {
    $count = (int)$db->query("SELECT COUNT(*) AS c FROM quran_surahs")->fetch()['c'];
    if ($count === 0) {
        $seed = [
            [1,'Al-Fatihah',7], [2,'Al-Baqarah',286], [3,'Al-Imran',200], [4,'An-Nisa',176],
            [5,"Al-Ma'idah",120], [6,'Al-Anam',165], [7,"Al-A'raf",206], [8,'Al-Anfal',75],
            [9,'At-Tawbah',129], [10,'Yunus',109], [11,'Hud',123], [12,'Yusuf',111],
            [13,"Ar-Ra'd",43], [14,'Ibrahim',52], [15,'Al-Hijr',99], [16,'An-Nahl',128],
            [17,'Al-Isra',111], [18,'Al-Kahf',110], [19,'Maryam',98], [20,'Ta-Ha',135],
            [21,'Al-Anbiya',112], [22,'Al-Hajj',78], [23,"Al-Mu'minun",118], [24,'An-Nur',64],
            [25,'Al-Furqan',77], [26,'Ash-Shuara',227], [27,'An-Naml',93], [28,'Al-Qasas',88],
            [29,'Al-Ankabut',69], [30,'Ar-Rum',60], [31,'Luqman',34], [32,'As-Sajdah',30],
            [33,'Al-Ahzab',73], [34,'Saba',54], [35,'Fatir',45], [36,'Ya-Sin',83],
            [37,'As-Saffat',182], [38,'Sad',88], [39,'Az-Zumar',75], [40,'Ghafir',85],
            [41,'Fussilat',54], [42,'Ash-Shura',53], [43,'Az-Zukhruf',89], [44,'Ad-Dukhan',59],
            [45,'Al-Jathiyah',37], [46,'Al-Ahqaf',35], [47,'Muhammad',38], [48,'Al-Fath',29],
            [49,'Al-Hujurat',18], [50,'Qaf',45], [51,'Adh-Dhariyat',60], [52,'At-Tur',49],
            [53,'An-Najm',62], [54,'Al-Qamar',55], [55,'Ar-Rahman',78], [56,"Al-Waqi'ah",96],
            [57,'Al-Hadid',29], [58,'Al-Mujadila',22], [59,'Al-Hashr',24], [60,'Al-Mumtahanah',13],
            [61,'As-Saff',14], [62,"Al-Jumu'ah",11], [63,'Al-Munafiqun',11], [64,'At-Taghabun',18],
            [65,'At-Talaq',12], [66,'At-Tahrim',12], [67,'Al-Mulk',30], [68,'Al-Qalam',52],
            [69,"Al-Haqqah",52], [70,"Al-Ma'arij",44], [71,'Nuh',28], [72,'Al-Jinn',28],
            [73,'Al-Muzzammil',20], [74,'Al-Muddaththir',56], [75,'Al-Qiyamah',40], [76,'Al-Insan',31],
            [77,'Al-Mursalat',50], [78,'An-Naba',40], [79,"An-Nazi'at",46], [80,'Abasa',42],
            [81,'At-Takwir',29], [82,'Al-Infitar',19], [83,'Al-Mutaffifin',36], [84,'Al-Inshiqaq',25],
            [85,'Al-Buruj',22], [86,'At-Tariq',17], [87,"Al-A'la",19], [88,'Al-Ghashiyah',26],
            [89,'Al-Fajr',30], [90,'Al-Balad',20], [91,'Ash-Shams',15], [92,'Al-Layl',21],
            [93,'Ad-Duha',11], [94,'Ash-Sharh',8], [95,'At-Tin',8], [96,'Al-Alaq',19],
            [97,'Al-Qadr',5], [98,'Al-Bayyinah',8], [99,'Az-Zalzalah',8], [100,"Al-Adiyat",11],
            [101,"Al-Qari'ah",11], [102,'At-Takathur',8], [103,'Al-Asr',3], [104,'Al-Humazah',9],
            [105,'Al-Fil',5], [106,'Quraysh',4], [107,"Al-Ma'un",7], [108,'Al-Kawthar',3],
            [109,'Al-Kafirun',6], [110,'An-Nasr',3], [111,'Al-Masad',5], [112,'Al-Ikhlas',4],
            [113,'Al-Falaq',5], [114,'An-Nas',6]
        ];
        $ins = $db->prepare("INSERT INTO quran_surahs (surah_number, surah_name_ar, surah_name_en, ayah_count) VALUES (?,?,?,?)");
        foreach ($seed as $s) {
            $ins->execute([$s[0], null, $s[1], $s[2]]);
        }
    }
} catch (PDOException $e) {
    // ignore seeding errors silently
}

// Handle Add/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM quran_surahs WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Surah deleted');
            redirect(SITE_URL . '/modules/quran/manage_surahs.php');
        } catch (PDOException $e) {
            $error = 'Error deleting surah: ' . $e->getMessage();
        }
    } else {
        $surah_number = (int)($_POST['surah_number'] ?? 0);
        $surah_name_ar = sanitize($_POST['surah_name_ar'] ?? '');
        $surah_name_en = sanitize($_POST['surah_name_en'] ?? '');
        $ayah_count = (int)($_POST['ayah_count'] ?? 0);

        if (isset($_POST['add'])) {
            try {
                $stmt = $db->prepare("INSERT INTO quran_surahs (surah_number, surah_name_ar, surah_name_en, ayah_count) VALUES (?,?,?,?)");
                $stmt->execute([$surah_number, $surah_name_ar, $surah_name_en, $ayah_count ?: null]);
                setFlash('success', 'Surah added');
                redirect(SITE_URL . '/modules/quran/manage_surahs.php');
            } catch (PDOException $e) {
                $error = 'Error adding surah: ' . $e->getMessage();
            }
        }

        if (isset($_POST['update'])) {
            $id = (int)$_POST['id'];
            try {
                $stmt = $db->prepare("UPDATE quran_surahs SET surah_number = ?, surah_name_ar = ?, surah_name_en = ?, ayah_count = ? WHERE id = ?");
                $stmt->execute([$surah_number, $surah_name_ar, $surah_name_en, $ayah_count ?: null, $id]);
                setFlash('success', 'Surah updated');
                redirect(SITE_URL . '/modules/quran/manage_surahs.php');
            } catch (PDOException $e) {
                $error = 'Error updating surah: ' . $e->getMessage();
            }
        }
    }
}

// Edit state
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM quran_surahs WHERE id = ?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}

// List all surahs
$stmt = $db->query("SELECT * FROM quran_surahs ORDER BY surah_number");
$surahs = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-book"></i> Manage Surahs</h1>
    <p class="subtitle">Add, edit and delete Surahs</p>
    <p><a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-arrow-left"></i> Back to Progress</a></p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-header"><h3><?php echo $edit ? 'Edit Surah' : 'Add New Surah'; ?></h3></div>
    <div class="card-body">
        <form method="POST">
            <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?php echo (int)$edit['id']; ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="surah_number">Surah Number *</label>
                    <input type="number" min="1" max="114" name="surah_number" id="surah_number" class="form-control" value="<?php echo htmlspecialchars($edit['surah_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="surah_name_en">Surah Name (English) *</label>
                    <input type="text" name="surah_name_en" id="surah_name_en" class="form-control" value="<?php echo htmlspecialchars($edit['surah_name_en'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="surah_name_ar">Surah Name (Arabic)</label>
                    <input type="text" name="surah_name_ar" id="surah_name_ar" class="form-control" value="<?php echo htmlspecialchars($edit['surah_name_ar'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="ayah_count">Ayah Count</label>
                    <input type="number" min="1" name="ayah_count" id="ayah_count" class="form-control" value="<?php echo htmlspecialchars($edit['ayah_count'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <?php if ($edit): ?>
                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="manage_surahs.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <?php else: ?>
                <button type="submit" name="add" class="btn btn-success"><i class="fas fa-plus"></i> Add Surah</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header"><h3><i class="fas fa-list"></i> All Surahs</h3></div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name (EN)</th>
                    <th>Name (AR)</th>
                    <th>Ayahs</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($surahs as $s): ?>
                <tr>
                    <td><?php echo (int)$s['surah_number']; ?></td>
                    <td><?php echo ($s['surah_name_en']); ?></td>
                    <td><?php echo ($s['surah_name_ar']) ?? '....' ; ?></td>
                    <td><?php echo ($s['ayah_count']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="manage_surahs.php?edit=<?php echo (int)$s['id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Delete this Surah?');">
                            <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
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
