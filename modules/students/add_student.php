<?php
/**
 * Add Student
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check permission
if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Add Student';
$success = '';
$error = '';

// Get classes
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = generateUniqueId('STD-');
    $admission_no = sanitize($_POST['admission_no']);
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $father_name = sanitize($_POST['father_name']);
    $cnic_bform = sanitize($_POST['cnic_bform']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    $student_type = sanitize($_POST['student_type'] ?? 'day_scholar');
    $class_id = isset($_POST['class_id']) && $_POST['class_id'] !== '' ? (int)$_POST['class_id'] : null;
    $admission_date = sanitize($_POST['admission_date']);
    $guardian_name = sanitize($_POST['guardian_name']);
    $guardian_phone = sanitize($_POST['guardian_phone']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $previous_education = sanitize($_POST['previous_education']);
    $medical_info = sanitize($_POST['medical_info']);
    $email = sanitize($_POST['email'] ?? '');
    $mother_name = sanitize($_POST['mother_name'] ?? '');
    // Same value as admission_date (DB has both columns per schema; one field in the form avoids duplication)
    $date_of_admission = $admission_date;
    $father_cnic = sanitize($_POST['father_cnic'] ?? '');
    $father_profession = sanitize($_POST['father_profession'] ?? '');
    $admission_challan_no = sanitize($_POST['admission_challan_no'] ?? '');
    $guardian_phone_2 = sanitize($_POST['guardian_phone_2'] ?? '');
    $whatsapp_no = sanitize($_POST['whatsapp_no'] ?? '');
    $previous_school_class = sanitize($_POST['previous_school_class'] ?? '');
    $current_school_class = isset($_POST['current_school_class']) && $_POST['current_school_class'] !== '' ? (int) $_POST['current_school_class'] : null;
    $date_of_leaving = sanitize($_POST['date_of_leaving'] ?? '');
    $reason_of_leaving = sanitize($_POST['reason_of_leaving'] ?? '');
    $total_marks = isset($_POST['total_marks']) && $_POST['total_marks'] !== '' ? (int)$_POST['total_marks'] : null;
    $obtained_marks = isset($_POST['obtained_marks']) && $_POST['obtained_marks'] !== '' ? (int)$_POST['obtained_marks'] : null;
    $class_status = sanitize($_POST['class_status'] ?? '');
    $class_status = in_array($class_status, ['pass', 'fail'], true) ? $class_status : null;

    // Login account creation (optional)
    $create_login = isset($_POST['create_login']) && $_POST['create_login'] == '1';
    $username = '';
    $password = '';
    $user_id = null;
    
    if ($create_login) {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required when creating login account';
        } else {
            // Check if username exists
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists';
            }
        }
    }

    // Photo upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload = uploadFile($_FILES['photo'], PHOTO_PATH);
        if ($upload['success']) {
            $photo = $upload['filename'];
        }
    }

    $previous_result_card = '';
    if (isset($_FILES['previous_result_card']) && $_FILES['previous_result_card']['error'] == 0) {
        $upload = uploadFile($_FILES['previous_result_card'], PHOTO_PATH);
        if ($upload['success']) {
            $previous_result_card = $upload['filename'];
        }
    }

    $date_of_leaving = $date_of_leaving !== '' ? $date_of_leaving : null;

    if (empty($error)) {
        try {
            $db->beginTransaction();
            
            // Create login account if requested
            if ($create_login && !empty($username) && !empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $full_name = $first_name . ' ' . $last_name;
                
                $sql = "INSERT INTO users (username, password, email, full_name, role, status) 
                        VALUES (?, ?, ?, ?, 'student', 'active')";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username, $hashed_password, $email, $full_name]);
                $user_id = $db->lastInsertId();
            }
            
            $sql = "INSERT INTO students (user_id, student_id, admission_no, first_name, last_name, father_name,
                    cnic_bform, date_of_birth, gender, student_type, class_id, admission_date, guardian_name,
                    guardian_phone, phone, address, city, previous_education, medical_info, photo,
                    mother_name, date_of_admission, date_of_leaving, reason_of_leaving, father_profession, father_cnic,
                    admission_challan_no, guardian_phone_2, whatsapp_no, previous_result_card, total_marks, obtained_marks,
                    class_status, previous_school_class, current_school_class)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $user_id, $student_id, $admission_no, $first_name, $last_name, $father_name,
                $cnic_bform, $date_of_birth, $gender, $student_type, $class_id, $admission_date,
                $guardian_name, $guardian_phone, $phone, $address, $city,
                $previous_education, $medical_info, $photo,
                $mother_name ?: null, $date_of_admission, $date_of_leaving,
                $reason_of_leaving ?: null, $father_profession ?: null, $father_cnic ?: null,
                $admission_challan_no ?: null, $guardian_phone_2 ?: null, $whatsapp_no ?: null,
                $previous_result_card ?: null, $total_marks, $obtained_marks, $class_status,
                $previous_school_class ?: null, $current_school_class ?: null
            ]);

            $db->commit();
            
            logActivity($db, 'Add Student', 'Students', "Added student: $first_name $last_name");

            setFlash('success', 'Student added successfully!');
            redirect(SITE_URL . '/modules/students/students.php');
        } catch(PDOException $e) {
            $db->rollBack();
            $error = 'Error adding student: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Add New Student</h1>
    <p class="subtitle">Enter student information</p>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <h3 class="mb-20">Personal Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="admission_no">Admission Number *</label>
                    <input type="text" name="admission_no" id="admission_no" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="admission_date">Admission Date *</label>
                    <input type="date" name="admission_date" id="admission_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

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
                    <label for="father_name">Father Name *</label>
                    <input type="text" name="father_name" id="father_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="cnic_bform">CNIC / B-Form</label>
                    <input type="text" name="cnic_bform" id="cnic_bform" class="form-control" placeholder="XXXXX-XXXXXXX-X">
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
                <div class="form-group">
                    <label for="student_type">Student Type *</label>
                    <select name="student_type" id="student_type" class="form-control" required>
                        <option value="day_scholar">Day Scholar</option>
                        <option value="boarder">Boarder</option>
                        <option value="border">Border</option>
                        <option value="orphan">Orphan</option>
                        <option value="aghosh">Aghosh</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mother_name">Mother Name</label>
                    <input type="text" name="mother_name" id="mother_name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="father_cnic">Father / Guardian CNIC</label>
                    <input type="text" name="father_cnic" id="father_cnic" class="form-control" placeholder="XXXXX-XXXXXXX-X">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="father_profession">Father Profession</label>
                    <input type="text" name="father_profession" id="father_profession" class="form-control">
                </div>
                <div class="form-group">
                    <label for="admission_challan_no">Admission Challan No</label>
                    <input type="text" name="admission_challan_no" id="admission_challan_no" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="class_id">Class</label>
                    <select name="class_id" id="class_id" class="form-control">
                        <option value="">Not assigned yet</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo $class['class_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <h3 class="mb-20 mt-20">Contact Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="guardian_name">Guardian Name *</label>
                    <input type="text" name="guardian_name" id="guardian_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="guardian_phone">Guardian Phone *</label>
                    <input type="tel" name="guardian_phone" id="guardian_phone" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="guardian_phone_2">Guardian Phone 2</label>
                    <input type="tel" name="guardian_phone_2" id="guardian_phone_2" class="form-control">
                </div>
                <div class="form-group">
                    <label for="whatsapp_no">WhatsApp</label>
                    <input type="tel" name="whatsapp_no" id="whatsapp_no" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Student Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control">
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
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control"></textarea>
            </div>

            <h3 class="mb-20 mt-20">Login Account (Optional)</h3>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="create_login" id="create_login" value="1"> Create Login Account
                </label>
            </div>

            <div id="login_fields" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" name="username" id="username" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                </div>
            </div>

            <h3 class="mb-20 mt-20">Previous school &amp; last result</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="previous_school_class">Previous school class</label>
                    <select name="previous_school_class" id="previous_school_class" class="form-control">
                        <option value="">—</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                        <option value="Class <?php echo $i; ?>">Class <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="current_school_class">Current class (at admission)</label>
                    <select name="current_school_class" id="current_school_class" class="form-control">
                        <option value="">Select Current School Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>">
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_marks">Last result — total marks</label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label for="obtained_marks">Last result — obtained marks</label>
                    <input type="number" name="obtained_marks" id="obtained_marks" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label for="class_status">Last result — status</label>
                    <select name="class_status" id="class_status" class="form-control">
                        <option value="">—</option>
                        <option value="pass">Pass</option>
                        <option value="fail">Fail</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="previous_result_card">Previous result card (image)</label>
                <input type="file" name="previous_result_card" id="previous_result_card" class="form-control" accept="image/jpeg,image/png,image/jpg">
            </div>

            <h3 class="mb-20 mt-20">Leaving (if applicable)</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_leaving">Date of leaving</label>
                    <input type="date" name="date_of_leaving" id="date_of_leaving" class="form-control">
                </div>
                <div class="form-group">
                    <label for="reason_of_leaving">Reason of leaving</label>
                    <input type="text" name="reason_of_leaving" id="reason_of_leaving" class="form-control">
                </div>
            </div>

            <h3 class="mb-20 mt-20">Additional Information</h3>

            <div class="form-group">
                <label for="previous_education">Previous Education</label>
                <textarea name="previous_education" id="previous_education" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="medical_info">Medical Information</label>
                <textarea name="medical_info" id="medical_info" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Student
                </button>
                <a href="students.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('create_login').addEventListener('change', function() {
    const loginFields = document.getElementById('login_fields');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    
    if (this.checked) {
        loginFields.style.display = 'block';
        username.required = true;
        password.required = true;
    } else {
        loginFields.style.display = 'none';
        username.required = false;
        password.required = false;
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
