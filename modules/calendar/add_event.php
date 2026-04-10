<?php
/**
 * Add Academic Event
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Add Event';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $event_date = sanitize($_POST['event_date']);
    $event_type = sanitize($_POST['event_type']);
    $is_recurring = sanitize($_POST['is_recurring'] ?? 'no');

    try {
        $sql = "INSERT INTO academic_events (title, description, event_date, event_type, is_recurring, created_by)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $title, $description, $event_date, $event_type, $is_recurring,
            $_SESSION['user_id']
        ]);

        logActivity($db, 'Add Event', 'Calendar', "Added event: $title");
        setFlash('success', 'Event added successfully!');
        redirect(SITE_URL . '/modules/calendar/index.php');
    } catch(PDOException $e) {
        $error = 'Error adding event: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Add Academic Event</h1>
    <p class="subtitle">Create a new calendar event</p>
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
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="event_date">Event Date *</label>
                    <input type="date" name="event_date" id="event_date" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event_type">Event Type *</label>
                    <select name="event_type" id="event_type" class="form-control" required>
                        <option value="holiday">Holiday</option>
                        <option value="exam">Exam</option>
                        <option value="event">Event</option>
                        <option value="meeting">Meeting</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="is_recurring">Recurring Event</label>
                    <select name="is_recurring" id="is_recurring" class="form-control">
                        <option value="no">No</option>
                        <option value="yes">Yes (Every Year)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Event
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
