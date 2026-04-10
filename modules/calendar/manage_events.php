<?php
/**
 * Manage Academic Events
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Manage Events';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filterType = isset($_GET['type']) ? sanitize($_GET['type']) : '';

$sql = "SELECT e.*, u.username as created_by_name 
        FROM academic_events e 
        LEFT JOIN users u ON e.created_by = u.id 
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filterType) {
    $sql .= " AND e.event_type = ?";
    $params[] = $filterType;
}

$sql .= " ORDER BY e.event_date DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

if (isset($_GET['delete']) && hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    $deleteId = (int)$_GET['delete'];
    try {
        $sql = "DELETE FROM academic_events WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$deleteId]);
        
        logActivity($db, 'Delete Event', 'Calendar', "Deleted event ID: $deleteId");
        setFlash('success', 'Event deleted successfully!');
        redirect(SITE_URL . '/modules/calendar/manage_events.php');
    } catch(PDOException $e) {
        setFlash('danger', 'Error deleting event: ' . $e->getMessage());
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Manage Events</h1>
    <p class="subtitle">View, edit or delete calendar events</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-inline" style="gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="type" class="form-control">
                <option value="">All Types</option>
                <option value="holiday" <?php echo $filterType == 'holiday' ? 'selected' : ''; ?>>Holiday</option>
                <option value="exam" <?php echo $filterType == 'exam' ? 'selected' : ''; ?>>Exam</option>
                <option value="event" <?php echo $filterType == 'event' ? 'selected' : ''; ?>>Event</option>
                <option value="meeting" <?php echo $filterType == 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                <option value="other" <?php echo $filterType == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a href="add_event.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Event
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Recurring</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No events found</td>
                </tr>
                <?php else: ?>
                <?php foreach ($events as $event): 
                    $typeClass = [
                        'holiday' => 'badge-success',
                        'exam' => 'badge-danger',
                        'event' => 'badge-primary',
                        'meeting' => 'badge-warning',
                        'other' => 'badge-secondary'
                    ][$event['event_type']] ?? 'badge-secondary';
                ?>
                <tr>
                    <td><?php echo formatDate($event['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                    <td><span class="badge <?php echo $typeClass; ?>"><?php echo ucfirst($event['event_type']); ?></span></td>
                    <td><?php echo $event['is_recurring'] == 'yes' ? '<span class="badge badge-info">Yes</span>' : 'No'; ?></td>
                    <td><?php echo $event['created_by_name'] ?? 'System'; ?></td>
                    <td>
                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?delete=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
