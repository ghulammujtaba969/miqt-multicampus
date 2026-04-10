<?php
/**
 * Edit Parent
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Parent';

$parent_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get parent details
$sql = "SELECT p.*, u.username, u.status as user_status FROM parents p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$parent_id]);
$parent = $stmt->fetch();

if (!$parent) {
    setFlash('danger', 'Parent not found');
    redirect(SITE_URL . '/modules/students/parents.php');
}

// Get linked students
$sql = "SELECT psr.*, s.student_id, s.first_name, s.last_name, s.class_id, c.class_name
        FROM parent_student_relation psr
        JOIN students s ON psr.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE psr.parent_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$parent_id]);
$linked_students = $stmt->fetchAll();

// Get all students for linking
$sql = "SELECT id, student_id, first_name, last_name, class_id FROM students WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$all_students = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    
    // Update login account if exists
    if ($parent['user_id']) {
        $full_name = $first_name . ' ' . $last_name;
        $sql = "UPDATE users SET email = ?, full_name = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email, $full_name, $parent['user_id']]);
    }

    try {
        $db->beginTransaction();
        
        // Update parent
        $sql = "UPDATE parents SET first_name = ?, last_name = ?, cnic = ?, phone = ?, email = ?,
                address = ?, city = ?, relation = ?, occupation = ?, status = ?
                WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $first_name, $last_name, $cnic, $phone, $email,
            $address, $city, $relation, $occupation, $status, $parent_id
        ]);
        
        // Update student relations
        // First, delete existing relations
        $sql = "DELETE FROM parent_student_relation WHERE parent_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$parent_id]);
        
        // Then, add new relations
        if (isset($_POST['students']) && is_array($_POST['students'])) {
            foreach ($_POST['students'] as $student_id) {
                $student_id = (int)$student_id;
                $relation_type = sanitize($_POST['relation_' . $student_id] ?? $relation);
                $is_primary = isset($_POST['primary_' . $student_id]) ? 1 : 0;
                
                $sql = "INSERT INTO parent_student_relation (parent_id, student_id, relation_type, is_primary) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$parent_id, $student_id, $relation_type, $is_primary]);
            }
        }
        
        $db->commit();
        
        logActivity($db, 'Edit Parent', 'Students', "Updated parent: $first_name $last_name");
        setFlash('success', 'Parent updated successfully!');
        redirect(SITE_URL . '/modules/students/view_parent.php?id=' . $parent_id);
    } catch(PDOException $e) {
        $db->rollBack();
        $error = 'Error updating parent: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Parent</h1>
    <p class="subtitle"><?php echo $parent['parent_id']; ?> - <?php echo $parent['first_name'] . ' ' . $parent['last_name']; ?></p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <h3 class="mb-20">Personal Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control"
                           value="<?php echo htmlspecialchars($parent['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                           value="<?php echo htmlspecialchars($parent['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cnic">CNIC</label>
                    <input type="text" name="cnic" id="cnic" class="form-control"
                           value="<?php echo htmlspecialchars($parent['cnic'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="relation">Relation *</label>
                    <select name="relation" id="relation" class="form-control" required>
                        <option value="father" <?php echo ($parent['relation'] == 'father') ? 'selected' : ''; ?>>Father</option>
                        <option value="mother" <?php echo ($parent['relation'] == 'mother') ? 'selected' : ''; ?>>Mother</option>
                        <option value="guardian" <?php echo ($parent['relation'] == 'guardian') ? 'selected' : ''; ?>>Guardian</option>
                        <option value="other" <?php echo ($parent['relation'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <input type="text" name="occupation" id="occupation" class="form-control"
                           value="<?php echo htmlspecialchars($parent['occupation'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($parent['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($parent['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <h3 class="mb-20 mt-20">Contact Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           value="<?php echo htmlspecialchars($parent['phone']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="<?php echo htmlspecialchars($parent['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control"
                           value="<?php echo htmlspecialchars($parent['city'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"><?php echo htmlspecialchars($parent['address'] ?? ''); ?></textarea>
            </div>

            <?php if ($parent['username']): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Login Account: <strong><?php echo htmlspecialchars($parent['username']); ?></strong>
                <a href="<?php echo SITE_URL; ?>/modules/users/edit_user.php?id=<?php echo $parent['user_id']; ?>" class="btn btn-sm btn-primary" style="margin-left: 10px;">
                    <i class="fas fa-edit"></i> Edit Login Account
                </a>
            </div>
            <?php endif; ?>

            <h3 class="mb-20 mt-20">Link to Students</h3>
            <div class="form-group">
                <label>Select Students</label>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    <?php if (count($all_students) > 0): ?>
                        <?php 
                        $linked_student_ids = array_column($linked_students, 'student_id');
                        foreach ($all_students as $student): 
                            $is_linked = in_array($student['id'], $linked_student_ids);
                            $linked_data = null;
                            if ($is_linked) {
                                foreach ($linked_students as $ls) {
                                    if ($ls['student_id'] == $student['id']) {
                                        $linked_data = $ls;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <div class="form-check" style="margin-bottom: 8px;">
                            <input type="checkbox" name="students[]" value="<?php echo $student['id']; ?>" 
                                   id="student_<?php echo $student['id']; ?>" 
                                   class="form-check-input student-checkbox"
                                   <?php echo $is_linked ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="student_<?php echo $student['id']; ?>" style="margin-left: 5px;">
                                <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?>
                            </label>
                            <div style="margin-left: 25px; margin-top: 5px; <?php echo $is_linked ? '' : 'display: none;'; ?>" class="student-relation-<?php echo $student['id']; ?>">
                                <select name="relation_<?php echo $student['id']; ?>" class="form-control form-control-sm" style="width: 150px; display: inline-block;">
                                    <option value="father" <?php echo ($linked_data && $linked_data['relation_type'] == 'father') ? 'selected' : ''; ?>>Father</option>
                                    <option value="mother" <?php echo ($linked_data && $linked_data['relation_type'] == 'mother') ? 'selected' : ''; ?>>Mother</option>
                                    <option value="guardian" <?php echo ($linked_data && $linked_data['relation_type'] == 'guardian') ? 'selected' : ''; ?>>Guardian</option>
                                    <option value="other" <?php echo ($linked_data && $linked_data['relation_type'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <label style="margin-left: 10px;">
                                    <input type="checkbox" name="primary_<?php echo $student['id']; ?>" <?php echo ($linked_data && $linked_data['is_primary']) ? 'checked' : ''; ?>> Primary Contact
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No active students found</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group mt-20">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Parent
                </button>
                <a href="view_parent.php?id=<?php echo $parent_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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

