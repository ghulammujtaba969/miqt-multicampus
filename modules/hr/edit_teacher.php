<?php
/**
 * Edit Teacher
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Teacher';

$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get teacher details
$sql = "SELECT * FROM teachers WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    setFlash('danger', 'Teacher not found');
    redirect(SITE_URL . '/modules/hr/teachers.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $father_name = sanitize($_POST['father_name']);
    $cnic = sanitize($_POST['cnic']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $qualification = sanitize($_POST['qualification']);
    $specialization = sanitize($_POST['specialization']);
    $salary = sanitize($_POST['salary']);
    $employment_type = sanitize($_POST['employment_type']);
    $status = sanitize($_POST['status']);
    $reference_name = sanitize($_POST['reference_name'] ?? '');
    $reference_number = sanitize($_POST['reference_number'] ?? '');
    $past_history = sanitize($_POST['past_history'] ?? '');

    // Photo upload
    $photo = $teacher['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload = uploadFile($_FILES['photo'], PHOTO_PATH);
        if ($upload['success']) {
            if ($teacher['photo'] && file_exists(PHOTO_PATH . $teacher['photo'])) {
                unlink(PHOTO_PATH . $teacher['photo']);
            }
            $photo = $upload['filename'];
        }
    }

    try {
        $sql = "UPDATE teachers SET first_name = ?, last_name = ?, father_name = ?,
                cnic = ?, date_of_birth = ?, gender = ?, phone = ?, email = ?,
                address = ?, city = ?, qualification = ?, specialization = ?,
                salary = ?, employment_type = ?, photo = ?, status = ?,
                reference_name = ?, reference_number = ?, past_history = ?
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $first_name, $last_name, $father_name, $cnic, $date_of_birth,
            $gender, $phone, $email, $address, $city, $qualification,
            $specialization, $salary, $employment_type, $photo, $status,
            $reference_name, $reference_number, $past_history,
            $teacher_id
        ]);

        logActivity($db, 'Edit Teacher', 'HR', "Updated teacher: $first_name $last_name");
        setFlash('success', 'Teacher updated successfully!');
        redirect(SITE_URL . '/modules/hr/view_teacher.php?id=' . $teacher_id);
    } catch(PDOException $e) {
        $error = 'Error updating teacher: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="miqt-forms-page-header">
    <h2>Edit teacher</h2>
    <div class="miqt-breadcrumb-custom">
        <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php">Dashboard</a>
        <span class="sep">›</span>
        <a href="teachers.php">Teachers</a>
        <span class="sep">›</span>
        <a href="view_teacher.php?id=<?php echo (int)$teacher_id; ?>">View</a>
        <span class="sep">›</span>
        <span>Edit</span>
    </div>
</div>
<p class="text-muted small mb-3"><?php echo htmlspecialchars($teacher['teacher_id']); ?> — <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-user"></i></div>
        <div>
            <h3>Personal information</h3>
            <p>Identity, contact, and photo</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="father_name">Father Name</label>
                    <input type="text" name="father_name" id="father_name" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['father_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="cnic">CNIC *</label>
                    <input type="text" name="cnic" id="cnic" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['cnic']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                           value="<?php echo $teacher['date_of_birth']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="male" <?php echo ($teacher['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($teacher['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['phone']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['email']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['city']); ?>">
                </div>

                <div class="form-group">
                    <label for="photo">Photo (leave empty to keep current)</label>
                    <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"><?php echo htmlspecialchars($teacher['address']); ?></textarea>
            </div>

            <h3 class="mb-20 mt-20">Professional Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="qualification">Qualification *</label>
                    <input type="text" name="qualification" id="qualification" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['qualification']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" name="specialization" id="specialization" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['specialization']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="employment_type">Employment Type *</label>
                    <select name="employment_type" id="employment_type" class="form-control" required>
                        <option value="full_time" <?php echo ($teacher['employment_type'] == 'full_time') ? 'selected' : ''; ?>>Full Time</option>
                        <option value="part_time" <?php echo ($teacher['employment_type'] == 'part_time') ? 'selected' : ''; ?>>Part Time</option>
                        <option value="contract" <?php echo ($teacher['employment_type'] == 'contract') ? 'selected' : ''; ?>>Contract</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($teacher['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($teacher['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo ($teacher['status'] == 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="salary">Monthly Salary</label>
                <input type="number" name="salary" id="salary" class="form-control" step="0.01"
                       value="<?php echo $teacher['salary']; ?>">
            </div>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-user-friends"></i></div>
        <div>
            <h3>Reference &amp; history</h3>
            <p>References and prior experience</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="reference_name">Reference Name</label>
                    <input type="text" name="reference_name" id="reference_name" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['reference_name'] ?? ''); ?>"
                           placeholder="e.g., Abdul Qadir">
                </div>

                <div class="form-group">
                    <label for="reference_number">Reference Contact No</label>
                    <input type="text" name="reference_number" id="reference_number" class="form-control"
                           value="<?php echo htmlspecialchars($teacher['reference_number'] ?? ''); ?>"
                           placeholder="e.g., +92-XXX-XXXXXXX">
                </div>
            </div>

            <div class="form-group">
                <label for="past_history">Past History</label>
                <textarea name="past_history" id="past_history" class="form-control" rows="3"
                          placeholder="Previous teaching experience..."><?php echo htmlspecialchars($teacher['past_history'] ?? ''); ?></textarea>
            </div>

            <div class="miqt-form-actions-bar mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update teacher
                </button>
                <a href="view_teacher.php?id=<?php echo $teacher_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
    </div>
</div>
</form>

<?php require_once '../../includes/footer.php'; ?>
