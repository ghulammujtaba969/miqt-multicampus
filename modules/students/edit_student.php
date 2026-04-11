<?php
/**
 * Edit Student
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Student';

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get student details with user info
$sql = "SELECT s.*, csc.class_name as current_school_class_name, u.username, u.email as user_email, u.status as user_status FROM students s
        LEFT JOIN classes csc ON csc.id = s.current_school_class
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', 'Student not found');
    redirect(SITE_URL . '/modules/students/students.php');
}

// Get classes
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admission_no = sanitize($_POST['admission_no']);
    $admission_date = sanitize($_POST['admission_date']);
    $date_of_admission = $admission_date;
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $father_name = sanitize($_POST['father_name']);
    $cnic_bform = sanitize($_POST['cnic_bform']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    $student_type = sanitize($_POST['student_type'] ?? 'day_scholar');
    $class_id = isset($_POST['class_id']) && $_POST['class_id'] !== '' ? (int)$_POST['class_id'] : null;
    $guardian_name = sanitize($_POST['guardian_name']);
    $guardian_phone = sanitize($_POST['guardian_phone']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $previous_education = sanitize($_POST['previous_education']);
    $medical_info = sanitize($_POST['medical_info']);
    $status = sanitize($_POST['status']);
    $email = sanitize($_POST['email'] ?? '');
    $mother_name = sanitize($_POST['mother_name'] ?? '');
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
    $date_of_leaving = $date_of_leaving !== '' ? $date_of_leaving : null;

    // Login account management
    $create_login = isset($_POST['create_login']) && $_POST['create_login'] == '1';
    $update_login = isset($_POST['update_login']) && $_POST['update_login'] == '1';
    $username = '';
    $password = '';
    $user_id = $student['user_id'];
    
    if ($create_login && !$user_id) {
        // Create new login account
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
    } elseif ($update_login && $user_id) {
        // Update existing login account
        $username = sanitize($_POST['username']);
        $password = $_POST['password'] ?? '';
    }

    // Photo upload
    $photo = $student['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload = uploadFile($_FILES['photo'], PHOTO_PATH);
        if ($upload['success']) {
            // Delete old photo
            if ($student['photo'] && file_exists(PHOTO_PATH . $student['photo'])) {
                unlink(PHOTO_PATH . $student['photo']);
            }
            $photo = $upload['filename'];
        }
    }

    $previous_result_card = $student['previous_result_card'] ?? '';
    if (isset($_FILES['previous_result_card']) && $_FILES['previous_result_card']['error'] == 0) {
        $upload = uploadFile($_FILES['previous_result_card'], PHOTO_PATH);
        if ($upload['success']) {
            if (!empty($student['previous_result_card']) && file_exists(PHOTO_PATH . $student['previous_result_card'])) {
                unlink(PHOTO_PATH . $student['previous_result_card']);
            }
            $previous_result_card = $upload['filename'];
        }
    }

    if (empty($error)) {
        try {
            $db->beginTransaction();
            
            // Create login account if requested
            if ($create_login && !$user_id && !empty($username) && !empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $full_name = $first_name . ' ' . $last_name;
                
                $sql = "INSERT INTO users (username, password, email, full_name, role, status) 
                        VALUES (?, ?, ?, ?, 'student', 'active')";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username, $hashed_password, $email, $full_name]);
                $user_id = $db->lastInsertId();
            }
            
            // Update login account if requested
            if ($update_login && $user_id) {
                $full_name = $first_name . ' ' . $last_name;
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, password = ?, email = ?, full_name = ? WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$username, $hashed_password, $email, $full_name, $user_id]);
                } else {
                    $sql = "UPDATE users SET username = ?, email = ?, full_name = ? WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$username, $email, $full_name, $user_id]);
                }
            } elseif ($user_id && !$update_login) {
                // Just update email and name if account exists
                $full_name = $first_name . ' ' . $last_name;
                $sql = "UPDATE users SET email = ?, full_name = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$email, $full_name, $user_id]);
            }
            
            $sql = "UPDATE students SET user_id = ?, admission_no = ?, admission_date = ?, date_of_admission = ?,
                    first_name = ?, last_name = ?, father_name = ?,
                    cnic_bform = ?, date_of_birth = ?, gender = ?, student_type = ?, class_id = ?,
                    guardian_name = ?, guardian_phone = ?, phone = ?, address = ?, city = ?,
                    previous_education = ?, medical_info = ?, photo = ?, status = ?,
                    mother_name = ?, date_of_leaving = ?, reason_of_leaving = ?, father_profession = ?, father_cnic = ?,
                    admission_challan_no = ?, guardian_phone_2 = ?, whatsapp_no = ?, previous_result_card = ?,
                    total_marks = ?, obtained_marks = ?, class_status = ?, previous_school_class = ?, current_school_class = ?
                    WHERE id = ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $user_id, $admission_no, $admission_date, $date_of_admission,
                $first_name, $last_name, $father_name, $cnic_bform, $date_of_birth,
                $gender, $student_type, $class_id, $guardian_name, $guardian_phone, $phone,
                $address, $city, $previous_education, $medical_info, $photo, $status,
                $mother_name ?: null, $date_of_leaving, $reason_of_leaving ?: null, $father_profession ?: null, $father_cnic ?: null,
                $admission_challan_no ?: null, $guardian_phone_2 ?: null, $whatsapp_no ?: null, $previous_result_card ?: null,
                $total_marks, $obtained_marks, $class_status, $previous_school_class ?: null, $current_school_class ?: null,
                $student_id
            ]);

            $db->commit();

            logActivity($db, 'Edit Student', 'Students', "Updated student: $first_name $last_name");
            setFlash('success', 'Student updated successfully!');
            redirect(SITE_URL . '/modules/students/view_student.php?id=' . $student_id);
        } catch(PDOException $e) {
            $db->rollBack();
            $error = 'Error updating student: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="miqt-forms-page-header">
    <h2>Edit student</h2>
    <div class="miqt-breadcrumb-custom">
        <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php">Dashboard</a>
        <span class="sep">›</span>
        <a href="students.php">Students</a>
        <span class="sep">›</span>
        <a href="view_student.php?id=<?php echo (int)$student_id; ?>">View</a>
        <span class="sep">›</span>
        <span>Edit</span>
    </div>
</div>
<p class="text-muted small mb-3"><?php echo htmlspecialchars($student['student_id']); ?> — <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-user-graduate"></i></div>
        <div>
            <h3>Student record</h3>
            <p>Personal details, class, and status</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="admission_no">Admission Number *</label>
                    <input type="text" name="admission_no" id="admission_no" class="form-control"
                           value="<?php echo htmlspecialchars($student['admission_no']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="admission_date">Admission Date *</label>
                    <input type="date" name="admission_date" id="admission_date" class="form-control"
                           value="<?php echo htmlspecialchars($student['admission_date']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control"
                           value="<?php echo ($student['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                           value="<?php echo ($student['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="father_name">Father Name *</label>
                    <input type="text" name="father_name" id="father_name" class="form-control"
                           value="<?php echo ($student['father_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="cnic_bform">CNIC / B-Form (student)</label>
                    <input type="text" name="cnic_bform" id="cnic_bform" class="form-control"
                           value="<?php echo htmlspecialchars($student['cnic_bform'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mother_name">Mother Name</label>
                    <input type="text" name="mother_name" id="mother_name" class="form-control"
                           value="<?php echo htmlspecialchars($student['mother_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="father_cnic">Father / Guardian CNIC</label>
                    <input type="text" name="father_cnic" id="father_cnic" class="form-control"
                           value="<?php echo htmlspecialchars($student['father_cnic'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="father_profession">Father Profession</label>
                    <input type="text" name="father_profession" id="father_profession" class="form-control"
                           value="<?php echo htmlspecialchars($student['father_profession'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="admission_challan_no">Admission Challan No</label>
                    <input type="text" name="admission_challan_no" id="admission_challan_no" class="form-control"
                           value="<?php echo htmlspecialchars($student['admission_challan_no'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth *</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                           value="<?php echo $student['date_of_birth']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="male" <?php echo ($student['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($student['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="class_id">Class *</label>
                    <select name="class_id" id="class_id" class="form-control">
                        <option value="">No Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"
                                <?php echo ($student['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo $class['class_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="student_type">Student Type *</label>
                    <select name="student_type" id="student_type" class="form-control" required>
                        <option value="day_scholar" <?php echo ($student['student_type'] ?? '') == 'day_scholar' ? 'selected' : ''; ?>>Day Scholar</option>
                        <option value="boarder" <?php echo ($student['student_type'] ?? '') == 'boarder' ? 'selected' : ''; ?>>Boarder</option>
                        <option value="border" <?php echo ($student['student_type'] ?? '') == 'border' ? 'selected' : ''; ?>>Border</option>
                        <option value="orphan" <?php echo ($student['student_type'] ?? '') == 'orphan' ? 'selected' : ''; ?>>Orphan</option>
                        <option value="aghosh" <?php echo ($student['student_type'] ?? '') == 'aghosh' ? 'selected' : ''; ?>>Aghosh</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($student['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($student['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="graduated" <?php echo ($student['status'] == 'graduated') ? 'selected' : ''; ?>>Graduated</option>
                        <option value="alumni" <?php echo ($student['status'] == 'alumni') ? 'selected' : ''; ?>>Alumni</option>
                        <option value="left" <?php echo ($student['status'] == 'left') ? 'selected' : ''; ?>>Left</option>
                        <option value="expelled" <?php echo ($student['status'] == 'expelled') ? 'selected' : ''; ?>>Expelled</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="photo">Photo (leave empty to keep current)</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                <?php if ($student['photo']): ?>
                <small class="text-muted">Current photo:
                    <img src="<?php echo SITE_URL . '/uploads/photos/' . $student['photo']; ?>"
                         alt="Photo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                </small>
                <?php endif; ?>
            </div>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-address-book"></i></div>
        <div>
            <h3>Contact information</h3>
            <p>Guardian and student contact details</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="guardian_name">Guardian Name *</label>
                    <input type="text" name="guardian_name" id="guardian_name" class="form-control"
                           value="<?php echo ($student['guardian_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="guardian_phone">Guardian Phone *</label>
                    <input type="tel" name="guardian_phone" id="guardian_phone" class="form-control"
                           value="<?php echo htmlspecialchars($student['guardian_phone']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="guardian_phone_2">Guardian Phone 2</label>
                    <input type="tel" name="guardian_phone_2" id="guardian_phone_2" class="form-control"
                           value="<?php echo htmlspecialchars($student['guardian_phone_2'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="whatsapp_no">WhatsApp</label>
                    <input type="tel" name="whatsapp_no" id="whatsapp_no" class="form-control"
                           value="<?php echo htmlspecialchars($student['whatsapp_no'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Student Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="<?php echo htmlspecialchars($student['user_email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control"
                           value="<?php echo htmlspecialchars($student['city'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
            </div>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-key"></i></div>
        <div>
            <h3>Login account</h3>
            <p>Portal access for this student</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <?php if ($student['username']): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Login Account: <strong><?php echo htmlspecialchars($student['username']); ?></strong>
                <label style="margin-left: 15px;">
                    <input type="checkbox" name="update_login" id="update_login" value="1"> Update Login Account
                </label>
            </div>
            <div id="login_fields" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" name="username" id="username" class="form-control"
                               value="<?php echo htmlspecialchars($student['username']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">New Password (leave empty to keep current)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                </div>
            </div>
            <?php else: ?>
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
            <?php endif; ?>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-school"></i></div>
        <div>
            <h3>Previous school &amp; last result</h3>
            <p>Prior class and result card</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="previous_school_class">Previous school class</label>
                    <select name="previous_school_class" id="previous_school_class" class="form-control">
                        <?php
                        $psc = $student['previous_school_class'] ?? '';
                        $standardClasses = [];
                        for ($c = 1; $c <= 7; $c++) {
                            $standardClasses[] = 'Class ' . $c;
                        }
                        ?>
                        <option value="">—</option>
                        <?php if ($psc !== '' && !in_array($psc, $standardClasses, true)): ?>
                        <option value="<?php echo htmlspecialchars($psc); ?>" selected><?php echo htmlspecialchars($psc); ?> (other)</option>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= 7; $i++):
                            $val = 'Class ' . $i; ?>
                        <option value="<?php echo htmlspecialchars($val); ?>" <?php echo ($psc === $val) ? 'selected' : ''; ?>>Class <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="current_school_class">Current class (at admission)</label>
                    <select name="current_school_class" id="current_school_class" class="form-control">
                        <option value="">Select Current School Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"
                                <?php echo ((string) ($student['current_school_class'] ?? '') === (string) $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_marks">Last result — total marks</label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control" min="0"
                           value="<?php echo isset($student['total_marks']) && $student['total_marks'] !== null ? (int)$student['total_marks'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="obtained_marks">Last result — obtained marks</label>
                    <input type="number" name="obtained_marks" id="obtained_marks" class="form-control" min="0"
                           value="<?php echo isset($student['obtained_marks']) && $student['obtained_marks'] !== null ? (int)$student['obtained_marks'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="class_status">Last result — status</label>
                    <select name="class_status" id="class_status" class="form-control">
                        <option value="">—</option>
                        <option value="pass" <?php echo (($student['class_status'] ?? '') === 'pass') ? 'selected' : ''; ?>>Pass</option>
                        <option value="fail" <?php echo (($student['class_status'] ?? '') === 'fail') ? 'selected' : ''; ?>>Fail</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="previous_result_card">Previous result card (image — leave empty to keep current)</label>
                <input type="file" name="previous_result_card" id="previous_result_card" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <?php if (!empty($student['previous_result_card'])): ?>
                <small class="text-muted">Current file:
                    <a href="<?php echo SITE_URL . '/uploads/photos/' . htmlspecialchars($student['previous_result_card']); ?>" target="_blank">View</a>
                </small>
                <?php endif; ?>
            </div>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-door-open"></i></div>
        <div>
            <h3>Leaving (if applicable)</h3>
            <p>When the student has left the institute</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_leaving">Date of leaving</label>
                    <input type="date" name="date_of_leaving" id="date_of_leaving" class="form-control"
                           value="<?php echo htmlspecialchars($student['date_of_leaving'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="reason_of_leaving">Reason of leaving</label>
                    <input type="text" name="reason_of_leaving" id="reason_of_leaving" class="form-control"
                           value="<?php echo htmlspecialchars($student['reason_of_leaving'] ?? ''); ?>">
                </div>
            </div>
    </div>
</div>

<div class="miqt-form-section">
    <div class="miqt-form-section-header">
        <div class="miqt-section-icon"><i class="fas fa-notes-medical"></i></div>
        <div>
            <h3>Additional information</h3>
            <p>Education history, medical notes, and save</p>
        </div>
    </div>
    <div class="miqt-form-section-body">
            <div class="form-group">
                <label for="previous_education">Previous Education</label>
                <textarea name="previous_education" id="previous_education" class="form-control" rows="3"><?php echo htmlspecialchars($student['previous_education'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="medical_info">Medical Information</label>
                <textarea name="medical_info" id="medical_info" class="form-control" rows="3"><?php echo htmlspecialchars($student['medical_info'] ?? ''); ?></textarea>
            </div>

            <div class="miqt-form-actions-bar mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update student
                </button>
                <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
    </div>
</div>
</form>

<script>
const createLoginCheckbox = document.getElementById('create_login');
const updateLoginCheckbox = document.getElementById('update_login');
const loginFields = document.getElementById('login_fields');
const username = document.getElementById('username');
const password = document.getElementById('password');

if (createLoginCheckbox) {
    createLoginCheckbox.addEventListener('change', function() {
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
}

if (updateLoginCheckbox) {
    updateLoginCheckbox.addEventListener('change', function() {
        if (this.checked) {
            loginFields.style.display = 'block';
            username.required = true;
        } else {
            loginFields.style.display = 'none';
            username.required = false;
            password.required = false;
        }
    });
}
</script>

<?php require_once '../../includes/footer.php'; ?>
