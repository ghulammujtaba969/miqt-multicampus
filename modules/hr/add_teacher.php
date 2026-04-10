<?php
/**
 * Add Teacher
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Add Teacher';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = generateUniqueId('TCH-');
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
    $joining_date = sanitize($_POST['joining_date']);
    $salary = sanitize($_POST['salary']);
    $employment_type = sanitize($_POST['employment_type']);
    $reference_name = sanitize($_POST['reference_name'] ?? '');
    $reference_number = sanitize($_POST['reference_number'] ?? '');
    $past_history = sanitize($_POST['past_history'] ?? '');

    // Photo upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload = uploadFile($_FILES['photo'], PHOTO_PATH);
        if ($upload['success']) {
            $photo = $upload['filename'];
        }
    }

    try {
        $sql = "INSERT INTO teachers (teacher_id, first_name, last_name, father_name, cnic,
                date_of_birth, gender, phone, email, address, city, qualification, specialization,
                joining_date, salary, employment_type, photo, reference_name, reference_number, past_history, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $teacher_id, $first_name, $last_name, $father_name, $cnic,
            $date_of_birth, $gender, $phone, $email, $address, $city,
            $qualification, $specialization, $joining_date, $salary,
            $employment_type, $photo, $reference_name, $reference_number, $past_history
        ]);

        logActivity($db, 'Add Teacher', 'HR', "Added teacher: $first_name $last_name");
        setFlash('success', 'Teacher added successfully!');
        redirect(SITE_URL . '/modules/hr/teachers.php');
    } catch(PDOException $e) {
        $error = 'Error adding teacher: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Add New Teacher</h1>
    <p class="subtitle">Enter teacher information</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <h3 class="mb-20">Personal Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="father_name">Father Name</label>
                    <input type="text" name="father_name" id="father_name" class="form-control">
                </div>

                <div class="form-group">
                    <label for="cnic">CNIC *</label>
                    <input type="text" name="cnic" id="cnic" class="form-control" placeholder="XXXXX-XXXXXXX-X" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" name="phone" id="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control">
                </div>

                <div class="form-group">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"></textarea>
            </div>

            <h3 class="mb-20 mt-20">Professional Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="qualification">Qualification *</label>
                    <input type="text" name="qualification" id="qualification" class="form-control"
                           placeholder="e.g., MA Islamic Studies" required>
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" name="specialization" id="specialization" class="form-control"
                           placeholder="e.g., Qirat & Tajweed">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="joining_date">Joining Date *</label>
                    <input type="date" name="joining_date" id="joining_date" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="employment_type">Employment Type *</label>
                    <select name="employment_type" id="employment_type" class="form-control" required>
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="contract">Contract</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="salary">Monthly Salary (Optional)</label>
                <input type="number" name="salary" id="salary" class="form-control" step="0.01" min="0">
            </div>

            <h3 class="mb-20 mt-20">Reference Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="reference_name">Reference Name</label>
                    <input type="text" name="reference_name" id="reference_name" class="form-control"
                           placeholder="e.g., Abdul Qadir">
                </div>

                <div class="form-group">
                    <label for="reference_number">Reference Contact No</label>
                    <input type="text" name="reference_number" id="reference_number" class="form-control"
                           placeholder="e.g., +92-XXX-XXXXXXX">
                </div>
            </div>

            <div class="form-group">
                <label for="past_history">Past History</label>
                <textarea name="past_history" id="past_history" class="form-control" rows="3"
                          placeholder="Previous teaching experience..."></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Teacher
                </button>
                <a href="teachers.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
