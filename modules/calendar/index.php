<?php
/**
 * Academic Calendar
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Academic Calendar';

$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Get events for the current month
$sql = "SELECT * FROM academic_events 
        WHERE (event_date BETWEEN ? AND ?) 
        ORDER BY event_date";
$startDate = "$currentYear-" . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date("Y-m-t", strtotime($startDate));
$stmt = $db->prepare($sql);
$stmt->execute([$startDate, $endDate]);
$events = $stmt->fetchAll();

// Group events by date
$eventsByDate = [];
foreach ($events as $event) {
    $eventsByDate[$event['event_date']][] = $event;
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Academic Calendar</h1>
    <p class="subtitle"><?php echo date('F Y', strtotime($startDate)); ?></p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <div class="calendar-nav" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <?php 
                $prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
                $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
                $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
                ?>
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-secondary">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-secondary">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
                <a href="?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>" class="btn btn-primary">
                    Today
                </a>
            </div>
            <div>
                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <a href="add_event.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Event
                </a>
                <a href="manage_events.php" class="btn btn-info">
                    <i class="fas fa-cog"></i> Manage Events
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="calendar-legend" style="margin-bottom: 15px;">
            <span class="badge badge-success">Holiday</span>
            <span class="badge badge-danger">Exam</span>
            <span class="badge badge-primary">Event</span>
            <span class="badge badge-warning">Meeting</span>
            <span class="badge badge-secondary">Other</span>
        </div>

        <div class="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px;">
            <?php
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($daysOfWeek as $day) {
                echo '<div style="background: #f8f9fa; padding: 10px; text-align: center; font-weight: bold;">' . $day . '</div>';
            }

            $firstDay = date('w', strtotime($startDate));
            $daysInMonth = date('t', strtotime($startDate));
            
            for ($i = 0; $i < $firstDay; $i++) {
                echo '<div style="background: #f8f9fa; padding: 10px; min-height: 100px;"></div>';
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = "$currentYear-" . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday = $date == date('Y-m-d');
                $dayEvents = $eventsByDate[$date] ?? [];
                
                echo '<div style="background: ' . ($isToday ? '#e3f2fd' : '#fff') . '; border: 1px solid #ddd; padding: 5px; min-height: 100px; ' . ($isToday ? 'border-color: #2196F3;' : '') . '">';
                echo '<div style="font-weight: bold; margin-bottom: 5px;">' . $day . '</div>';
                
                foreach ($dayEvents as $event) {
                    $typeClass = [
                        'holiday' => 'badge-success',
                        'exam' => 'badge-danger',
                        'event' => 'badge-primary',
                        'meeting' => 'badge-warning',
                        'other' => 'badge-secondary'
                    ][$event['event_type']] ?? 'badge-secondary';
                    
                    echo '<div class="badge ' . $typeClass . '" style="display: block; margin-bottom: 2px; font-size: 10px; cursor: pointer;" 
                          title="' . htmlspecialchars($event['title']) . '">' 
                          . htmlspecialchars($event['title']) . '</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
.calendar-grid > div {
    overflow: hidden;
}
</style>

<?php require_once '../../includes/footer.php'; ?>
