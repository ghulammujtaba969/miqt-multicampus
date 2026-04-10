<?php
/**
 * Add Parent
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

$pageTitle = 'Add Parent';
$error = '';

// Get all students for linking
$sql = "SELECT id, student_id, first_name, last_name, class_id FROM students WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $parent_id = generateUniqueId('PAR-');
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $cnic = sanitize($_POST['cnic'] ?? '');
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $relation = sanitize($_POST['relation'] ?? 'father');
    $occupation = sanitize($_POST['occupation'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');
    
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

    if (empty($error)) {
        try {
            $db->beginTransaction();
            
            // Create login account if requested
            if ($create_login && !empty($username) && !empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $full_name = $first_name . ' ' . $last_name;
                
                $sql = "INSERT INTO users (username, password, email, full_name, role, status) 
                        VALUES (?, ?, ?, ?, 'parent', 'active')";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username, $hashed_password, $email, $full_name]);
                $user_id = $db->lastInsertId();
            }
            
            // Insert parent
            $sql = "INSERT INTO parents (user_id, parent_id, first_name, last_name, cnic, phone, email, 
                    address, city, relation, occupation, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $user_id, $parent_id, $first_name, $last_name, $cnic, $phone, $email,
                $address, $city, $relation, $occupation, $status
            ]);
            $parent_db_id = $db->lastInsertId();
            
            // Link to students if selected
            if (isset($_POST['students']) && is_array($_POST['students'])) {
                foreach ($_POST['students'] as $student_id) {
                    $student_id = (int)$student_id;
                    $relation_type = sanitize($_POST['relation_' . $student_id] ?? $relation);
                    $is_primary = isset($_POST['primary_' . $student_id]) ? 1 : 0;
                    
                    $sql = "INSERT INTO parent_student_relation (parent_id, student_id, relation_type, is_primary) 
                            VALUES (?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$parent_db_id, $student_id, $relation_type, $is_primary]);
                }
            }
            
            $db->commit();
            
            logActivity($db, 'Add Parent', 'Students', "Added parent: $first_name $last_name");
            setFlash('success', 'Parent added successfully!');
            redirect(SITE_URL . '/modules/students/parents.php');
        } catch(PDOException $e) {
            $db->rollBack();
            $error = 'Error adding parent: ' . $e->getMessage();
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Add New Parent</h1>
    <p class="subtitle">Enter parent information</p>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
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
                    <label for="cnic">CNIC</label>
                    <input type="text" name="cnic" id="cnic" class="form-control" placeholder="XXXXX-XXXXXXX-X">
                </div>

                <div class="form-group">
                    <label for="relation">Relation *</label>
                    <select name="relation" id="relation" class="form-control" required>
                        <option value="father">Father</option>
                        <option value="mother">Mother</option>
                        <option value="guardian">Guardian</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <input type="text" name="occupation" id="occupation" class="form-control">
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <h3 class="mb-20 mt-20">Contact Information</h3>

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
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"></textarea>
            </div>

            <h3 class="mb-20 mt-20">Link to Students</h3>
            <div class="form-group">
                <label>Select Students (Optional - can be linked later)</label>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                        <div class="form-check" style="margin-bottom: 8px;">
                            <input type="checkbox" name="students[]" value="<?php echo $student['id']; ?>" 
                                   id="student_<?php echo $student['id']; ?>" class="form-check-input student-checkbox">
                            <label class="form-check-label" for="student_<?php echo $student['id']; ?>" style="margin-left: 5px;">
                                <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?>
                            </label>
                            <div style="margin-left: 25px; margin-top: 5px; display: none;" class="student-relation-<?php echo $student['id']; ?>">
                                <select name="relation_<?php echo $student['id']; ?>" class="form-control form-control-sm" style="width: 150px; display: inline-block;">
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="guardian">Guardian</option>
                                    <option value="other">Other</option>
                                </select>
                                <label style="margin-left: 10px;">
                                    <input type="checkbox" name="primary_<?php echo $student['id']; ?>"> Primary Contact
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No active students found</p>
                    <?php endif; ?>
                </div>
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

            <div class="form-group mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Parent
                </button>
                <a href="parents.php" class="btn btn-secondary">
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

// Show relation fields when student is checked
document.querySelectorAll('.student-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const studentId = this.value;
        const relationDiv = document.querySelector('.student-relation-' + studentId);
        if (this.checked) {
            relationDiv.style.display = 'block';
        } else {
            relationDiv.style.display = 'none';
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>

