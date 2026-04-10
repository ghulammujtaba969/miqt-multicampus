<?php
/**
 * Import Students from CSV (Excel-compatible)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Import Students';
$results = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Error uploading file.';
    } else {
        $tmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if (!$handle) {
            $errors[] = 'Unable to read uploaded file.';
        } else {
            // Read header
            $rowNum = 0;
            $inserted = 0;
            $skipped = 0;
            $skippedRows = [];

            // Map of classes by name for quick lookup
            $stmt = $db->prepare("SELECT id, class_name FROM classes");
            $stmt->execute();
            $classMap = [];
            foreach ($stmt->fetchAll() as $c) {
                $classMap[mb_strtolower(trim($c['class_name']))] = (int)$c['id'];
            }

            // Expected headers (case-insensitive match by startswith for flexibility)
            $expected = [
                'admission no', 'first name', 'last name', 'father name', 'cnic',
                'date of birth', 'gender', 'student type', 'class name', 'admission date', 'guardian name',
                'guardian phone', 'phone', 'address', 'city', 'previous education', 'medical info', 'status'
            ];

            // read header
            $header = fgetcsv($handle);
            if ($header === false) {
                $errors[] = 'CSV appears empty.';
            } else {
                // Build index map
                $idx = [];
                foreach ($header as $i => $col) {
                    $lc = mb_strtolower(trim($col));
                    // Remove BOM if present (especially from first column)
                    $lc = str_replace("\xEF\xBB\xBF", '', $lc);
                    // Remove anything in parentheses and extra whitespace for better matching
                    $lc = preg_replace('/\s*\([^)]*\)\s*/', '', $lc);
                    $lc = trim($lc);
                    $idx[$lc] = $i;
                }


                // helper to get value by header key variants
                $get = function(array $row, array $keys) use ($idx) {
                    foreach ($keys as $k) {
                        foreach ($idx as $h => $i) {
                            if (strpos($h, $k) === 0) {
                                return isset($row[$i]) ? trim($row[$i]) : '';
                            }
                        }
                    }
                    return '';
                };

                $allowedStatuses = ['active','inactive','alumni','graduated','left','expelled'];

                // Helper function to convert various date formats to MySQL format (YYYY-MM-DD)
                $convertToMySQLDate = function($dateStr) {
                    if (empty($dateStr)) return '';

                    // If already in YYYY-MM-DD format, return as is
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                        return $dateStr;
                    }

                    // Try to parse various formats
                    $date = false;

                    // Try M/D/YYYY or MM/DD/YYYY format (common in Excel)
                    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $dateStr, $matches)) {
                        $date = DateTime::createFromFormat('m/d/Y', $dateStr);
                    }

                    // Try D-M-YYYY or DD-MM-YYYY format
                    if (!$date && preg_match('#^(\d{1,2})-(\d{1,2})-(\d{4})$#', $dateStr, $matches)) {
                        $date = DateTime::createFromFormat('d-m-Y', $dateStr);
                    }

                    // Try other common formats
                    if (!$date) {
                        $date = date_create($dateStr);
                    }

                    return $date ? $date->format('Y-m-d') : $dateStr;
                };

                // Process rows
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNum++;

                    $admission_no = $get($row, ['admission no']);
                    $first_name   = $get($row, ['first name']);
                    $last_name    = $get($row, ['last name']);
                    $father_name  = $get($row, ['father name']);
                    $cnic_bform   = $get($row, ['cnic', 'b-form']);
                    $dob          = $get($row, ['date of birth']);
                    $gender       = mb_strtolower($get($row, ['gender']));
                    $student_type = $get($row, ['student type']);
                    $class_name   = $get($row, ['class name']);
                    $admission_dt = $get($row, ['admission date']);
                    $guardian_name= $get($row, ['guardian name']);
                    $guardian_phone=$get($row, ['guardian phone']);
                    $phone        = $get($row, ['phone']);
                    $address      = $get($row, ['address']);
                    $city         = $get($row, ['city']);
                    $prev_edu     = $get($row, ['previous education']);
                    $medical_info = $get($row, ['medical info']);
                    $status       = mb_strtolower($get($row, ['status']));

                    // Convert date formats to YYYY-MM-DD if needed
                    $dob = $convertToMySQLDate($dob);
                    $admission_dt = $convertToMySQLDate($admission_dt);

                    // Basic validation
                    $missing = [];
                    if ($first_name === '') $missing[] = 'First Name';
                    if ($last_name === '') $missing[] = 'Last Name';
                    if ($father_name === '') $missing[] = 'Father Name';
                    if ($admission_no === '') $missing[] = 'Admission No';
                    if ($guardian_name === '') $missing[] = 'Guardian Name';
                    if ($guardian_phone === '') $missing[] = 'Guardian Phone';
                    if ($dob === '') $missing[] = 'Date of Birth';
                    if ($admission_dt === '') $missing[] = 'Admission Date';
                    if (!in_array($gender, ['male','female'])) $missing[] = 'Gender (must be male or female, got: "' . $gender . '")';

                    if (!empty($missing)) {
                        $skipped++;
                        $skippedRows[] = [
                            'row' => $rowNum + 1, // account for header row
                            'reason' => 'Missing/Invalid: ' . implode(', ', $missing)
                        ];
                        continue;
                    }

                    // Map class name -> id (optional)
                    $class_id = null;
                    if ($class_name !== '') {
                        $key = mb_strtolower(trim($class_name));
                        if (array_key_exists($key, $classMap)) {
                            $class_id = $classMap[$key];
                        } else {
                            // unknown class -> leave null
                        }
                    }

                    if (!in_array($status, $allowedStatuses)) {
                        $status = 'active';
                    }

                    $student_uid = generateUniqueId('STD-');

                    try {
                        $sql = "INSERT INTO students (
                                    student_id, admission_no, first_name, last_name, father_name,
                                    cnic_bform, date_of_birth, gender, student_type, class_id, admission_date,
                                    guardian_name, guardian_phone, phone, address, city,
                                    previous_education, medical_info, photo, status
                                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            $student_uid, $admission_no, $first_name, $last_name, $father_name,
                            $cnic_bform, $dob, $gender, $student_type, $class_id, $admission_dt,
                            $guardian_name, $guardian_phone, $phone, $address, $city,
                            $prev_edu, $medical_info, '', $status
                        ]);
                        $inserted++;
                    } catch (PDOException $e) {
                        $skipped++;
                        $skippedRows[] = [
                            'row' => $rowNum + 1,
                            'reason' => 'DB error: ' . $e->getMessage()
                        ];
                    }
                }
            }
            if (isset($handle) && is_resource($handle)) fclose($handle);

            $results = [
                'inserted' => $inserted,
                'skipped' => $skipped,
                'skipped_rows' => $skippedRows,
                'headers_found' => array_keys($idx)
            ];

            if ($inserted > 0) {
                logActivity($db, 'Import Students', 'Students', "Imported $inserted students (skipped $skipped)");
            }
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-import"></i> Import Students</h1>
    <p class="subtitle">Upload a CSV file exported from Excel</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach ($errors as $err) { echo '<div>' . ($err) . '</div>'; } ?>
    
</div>
<?php endif; ?>

<?php if ($results): ?>
<div class="alert alert-info">
    <strong>Import Summary:</strong>
    <div>Inserted: <?php echo (int)$results['inserted']; ?></div>
    <div>Skipped: <?php echo (int)$results['skipped']; ?></div>
    <?php if (!empty($results['headers_found'])): ?>
    <details style="margin-top:8px;">
        <summary>View CSV headers found</summary>
        <ul>
            <?php foreach ($results['headers_found'] as $h): ?>
            <li><?php echo ($h); ?></li>
            <?php endforeach; ?>
        </ul>
    </details>
    <?php endif; ?>
    <?php if (!empty($results['skipped_rows'])): ?>
    <details style="margin-top:8px;">
        <summary>View skipped details</summary>
        <ul>
            <?php foreach ($results['skipped_rows'] as $s): ?>
            <li>Row <?php echo (int)$s['row']; ?>: <?php echo ($s['reason']); ?></li>
            <?php endforeach; ?>
        </ul>
    </details>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <p>
            <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/students/students_import_sample.php">
                <i class="fas fa-download"></i> Download Sample CSV
            </a>
        </p>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Select CSV file (UTF-8)</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                <a href="<?php echo SITE_URL; ?>/modules/students/students.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            <div class="text-muted" style="margin-top:10px;">
                Expected columns (any order): Admission No, First Name, Last Name, Father Name, CNIC/B-Form, Date of Birth (YYYY-MM-DD), Gender (male/female), Student Type (optional), Class Name (optional), Admission Date (YYYY-MM-DD), Guardian Name, Guardian Phone, Phone, Address, City, Previous Education, Medical Info, Status.
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

