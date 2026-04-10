<?php
/**
 * Edit Academic Event
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Edit Event';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM academic_events WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    setFlash('danger', 'Event not found');
    redirect(SITE_URL . '/modules/calendar/manage_events.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $event_date = sanitize($_POST['event_date']);
    $event_type = sanitize($_POST['event_type']);
    $is_recurring = sanitize($_POST['is_recurring'] ?? 'no');

    try {
        $sql = "UPDATE academic_events SET title = ?, description = ?, event_date = ?, event_type = ?, is_recurring = ?
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $title, $description, $event_date, $event_type, $is_recurring,
            $event_id
        ]);

        logActivity($db, 'Edit Event', 'Calendar', "Updated event: $title");
        setFlash('success', 'Event updated successfully!');
        redirect(SITE_URL . '/modules/calendar/manage_events.php');
    } catch(PDOException $e) {
        $error = 'Error updating event: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Edit Event</h1>
    <p class="subtitle">Update calendar event</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_date">Event Date *</label>
                    <input type="date" name="event_date" id="event_date" class="form-control" value="<?php echo $event['event_date']; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event_type">Event Type *</label>
                    <select name="event_type" id="event_type" class="form-control" required>
                        <option value="holiday" <?php echo $event['event_type'] == 'holiday' ? 'selected' : ''; ?>>Holiday</option>
                        <option value="exam" <?php echo $event['event_type'] == 'exam' ? 'selected' : ''; ?>>Exam</option>
                        <option value="event" <?php echo $event['event_type'] == 'event' ? 'selected' : ''; ?>>Event</option>
                        <option value="meeting" <?php echo $event['event_type'] == 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                        <option value="other" <?php echo $event['event_type'] == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="is_recurring">Recurring Event</label>
                    <select name="is_recurring" id="is_recurring" class="form-control">
                        <option value="no" <?php echo $event['is_recurring'] == 'no' ? 'selected' : ''; ?>>No</option>
                        <option value="yes" <?php echo $event['is_recurring'] == 'yes' ? 'selected' : ''; ?>>Yes (Every Year)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Event
                </button>
                <a href="manage_events.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
