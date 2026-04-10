<?php
/**
 * View Upcoming Events (List)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Upcoming Events';

$filterType = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$sql = "SELECT * FROM academic_events WHERE event_date >= CURDATE()";
$params = [];

if ($filterType) {
    $sql .= " AND event_type = ?";
    $params[] = $filterType;
}

$sql .= " ORDER BY event_date ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Upcoming Events</h1>
    <p class="subtitle">List of upcoming academic events</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-inline" style="gap: 10px;">
            <select name="type" class="form-control">
                <option value="">All Types</option>
                <option value="holiday" <?php echo $filterType == 'holiday' ? 'selected' : ''; ?>>Holiday</option>
                <option value="exam" <?php echo $filterType == 'exam' ? 'selected' : ''; ?>>Exam</option>
                <option value="event" <?php echo $filterType == 'event' ? 'selected' : ''; ?>>Event</option>
                <option value="meeting" <?php echo $filterType == 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                <option value="other" <?php echo $filterType == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-calendar"></i> Calendar View
            </a>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (empty($events)): ?>
        <p class="text-center text-muted">No upcoming events</p>
        <?php else: ?>
        <div class="events-list">
            <?php foreach ($events as $event): 
                $typeClass = [
                    'holiday' => 'badge-success',
                    'exam' => 'badge-danger',
                    'event' => 'badge-primary',
                    'meeting' => 'badge-warning',
                    'other' => 'badge-secondary'
                ][$event['event_type']] ?? 'badge-secondary';
                
                $daysUntil = (strtotime($event['event_date']) - strtotime(date('Y-m-d'))) / 86400;
            ?>
            <div class="event-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee;">
                <div>
                    <h4 style="margin: 0 0 5px 0;">
                        <span class="badge <?php echo $typeClass; ?>"><?php echo ucfirst($event['event_type']); ?></span>
                        <?php echo htmlspecialchars($event['title']); ?>
                        <?php if ($event['is_recurring'] == 'yes'): ?>
                        <span class="badge badge-info"><i class="fas fa-redo"></i> Yearly</span>
                        <?php endif; ?>
                    </h4>
                    <?php if ($event['description']): ?>
                    <p class="text-muted" style="margin: 0;"><?php echo htmlspecialchars($event['description']); ?></p>
                    <?php endif; ?>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 18px; font-weight: bold; color: #2196F3;">
                        <?php echo formatDate($event['event_date']); ?>
                    </div>
                    <div class="text-muted">
                        <?php if ($daysUntil == 0): ?>
                        <span class="badge badge-success">Today</span>
                        <?php elseif ($daysUntil == 1): ?>
                        <span class="badge badge-primary">Tomorrow</span>
                        <?php else: ?>
                        <span class="text-muted"><?php echo $daysUntil; ?> days left</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
