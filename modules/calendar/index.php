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
if ($currentMonth < 1 || $currentMonth > 12) {
    $currentMonth = (int)date('n');
}
if ($currentYear < 2000 || $currentYear > 2100) {
    $currentYear = (int)date('Y');
}

$sql = "SELECT * FROM academic_events 
        WHERE (event_date BETWEEN ? AND ?) 
        ORDER BY event_date";
$startDate = "$currentYear-" . str_pad((string)$currentMonth, 2, '0', STR_PAD_LEFT) . "-01";
$endDate = date('Y-m-t', strtotime($startDate));
$stmt = $db->prepare($sql);
$stmt->execute([$startDate, $endDate]);
$events = $stmt->fetchAll();

$eventsByDate = [];
foreach ($events as $event) {
    $eventsByDate[$event['event_date']][] = $event;
}

function miqt_event_chip_class($type) {
    $map = [
        'holiday' => 'green',
        'exam' => 'blue',
        'event' => 'gold',
        'meeting' => 'purple',
        'other' => 'blue',
    ];
    return $map[$type] ?? 'blue';
}

function miqt_event_dot_class($type) {
    return miqt_event_chip_class($type);
}

function miqt_event_badge_class($type) {
    return miqt_event_chip_class($type);
}

$upcomingStmt = $db->query("SELECT * FROM academic_events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 8");
$upcomingEvents = $upcomingStmt ? $upcomingStmt->fetchAll() : [];

$prevMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
$prevYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;
$nextMonth = $currentMonth === 12 ? 1 : $currentMonth + 1;
$nextYear = $currentMonth === 12 ? $currentYear + 1 : $currentYear;

$monthLabel = date('F Y', strtotime($startDate));
$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$firstDay = (int)date('w', strtotime($startDate));
$daysInMonth = (int)date('t', strtotime($startDate));
$prevMonthDays = (int)date('t', strtotime("$prevYear-$prevMonth-01"));
$todayYmd = date('Y-m-d');

require_once '../../includes/header.php';
?>

<div class="miqt-cal-wrap">
    <div class="miqt-cal-main">
        <div class="miqt-cal-page-header">
            <div class="miqt-cal-nav">
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="miqt-cal-nav-btn" aria-label="Previous month"><i class="fas fa-chevron-left"></i></a>
                <div class="miqt-cal-month-label"><?php echo htmlspecialchars($monthLabel); ?></div>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="miqt-cal-nav-btn" aria-label="Next month"><i class="fas fa-chevron-right"></i></a>
                <a href="?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>" class="miqt-cal-nav-btn wide">Today</a>
            </div>
            <div class="miqt-view-tabs" role="tablist" aria-label="Calendar view">
                <span class="miqt-view-tab active" role="tab" aria-selected="true">Month</span>
                <span class="miqt-view-tab" role="tab" aria-selected="false">Week</span>
                <span class="miqt-view-tab" role="tab" aria-selected="false">Day</span>
                <span class="miqt-view-tab" role="tab" aria-selected="false">Agenda</span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a href="add_event.php" class="btn btn-success btn-sm"><i class="fas fa-plus me-1"></i> Add Event</a>
            <a href="manage_events.php" class="btn btn-info btn-sm"><i class="fas fa-cog me-1"></i> Manage Events</a>
            <?php endif; ?>
            <a href="view_events.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list me-1"></i> Upcoming list</a>
        </div>

        <div class="miqt-cal-card">
            <div class="miqt-cal-grid-header">
                <?php foreach ($daysOfWeek as $i => $day): ?>
                <div class="miqt-cal-day-name<?php echo ($i === 0 || $i === 6) ? ' weekend' : ''; ?>"><?php echo $day; ?></div>
                <?php endforeach; ?>
            </div>
            <div class="miqt-cal-grid">
                <?php
                for ($i = $firstDay - 1; $i >= 0; $i--) {
                    $d = $prevMonthDays - $i;
                    echo '<div class="miqt-cal-cell other-month"><div class="miqt-cal-date-num">' . $d . '</div></div>';
                }
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                    $isToday = $date === $todayYmd;
                    $dayEvents = $eventsByDate[$date] ?? [];
                    $cellClass = 'miqt-cal-cell' . ($isToday ? ' today' : '');
                    echo '<div class="' . $cellClass . '">';
                    echo '<div class="miqt-cal-date-num">' . $day . '</div>';
                    echo '<div class="miqt-cal-events">';
                    $slice = array_slice($dayEvents, 0, 2);
                    foreach ($slice as $ev) {
                        $chip = miqt_event_chip_class($ev['event_type']);
                        echo '<div class="miqt-cal-event-chip ' . htmlspecialchars($chip) . '" title="' . htmlspecialchars($ev['title']) . '">';
                        echo htmlspecialchars($ev['title']);
                        echo '</div>';
                    }
                    if (count($dayEvents) > 2) {
                        echo '<div class="small text-primary">+' . (count($dayEvents) - 2) . ' more</div>';
                    }
                    echo '</div></div>';
                }
                $totalCells = $firstDay + $daysInMonth;
                $remainder = $totalCells % 7;
                if ($remainder !== 0) {
                    for ($j = 1; $j <= 7 - $remainder; $j++) {
                        echo '<div class="miqt-cal-cell other-month"><div class="miqt-cal-date-num">' . $j . '</div></div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="miqt-cal-legend">
            <div class="miqt-cal-legend-item"><span class="miqt-cal-legend-dot" style="background:var(--miqt-cobalt)"></span> Exam</div>
            <div class="miqt-cal-legend-item"><span class="miqt-cal-legend-dot" style="background:var(--miqt-gold)"></span> Event</div>
            <div class="miqt-cal-legend-item"><span class="miqt-cal-legend-dot" style="background:#198754"></span> Holiday</div>
            <div class="miqt-cal-legend-item"><span class="miqt-cal-legend-dot" style="background:#dc3545"></span> Other / deadline</div>
            <div class="miqt-cal-legend-item"><span class="miqt-cal-legend-dot" style="background:#6a1f9e"></span> Meeting</div>
        </div>
    </div>

    <div class="miqt-cal-sidebar-right">
        <div class="miqt-widget-card">
            <div class="miqt-widget-header"><h6><i class="far fa-calendar me-1"></i> Quick navigation</h6></div>
            <div class="miqt-widget-body">
                <div class="miqt-cal-month-label text-center mb-2" style="font-size:0.95rem"><?php echo htmlspecialchars($monthLabel); ?></div>
                <div class="miqt-mini-grid-head">
                    <?php foreach (['S','M','T','W','T','F','S'] as $dn): ?>
                    <div class="miqt-mini-dname"><?php echo $dn; ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="miqt-mini-grid">
                    <?php
                    for ($i = $firstDay - 1; $i >= 0; $i--) {
                        $d = $prevMonthDays - $i;
                        echo '<div class="miqt-mini-cell other">' . $d . '</div>';
                    }
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                        $isToday = $date === $todayYmd;
                        $hasEv = !empty($eventsByDate[$date]);
                        $mc = 'miqt-mini-cell' . ($isToday ? ' today' : '') . ($hasEv ? ' has-event' : '');
                        echo '<div class="' . $mc . '">' . $day . '</div>';
                    }
                    $totalCells = $firstDay + $daysInMonth;
                    $remainder = $totalCells % 7;
                    if ($remainder !== 0) {
                        for ($j = 1; $j <= 7 - $remainder; $j++) {
                            echo '<div class="miqt-mini-cell other">' . $j . '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="miqt-widget-card">
            <div class="miqt-widget-header"><h6><i class="fas fa-plus-circle me-1"></i> Events</h6></div>
            <div class="miqt-widget-body">
                <p class="small text-muted mb-3">Add exams, holidays, and programmes to the shared calendar.</p>
                <a href="add_event.php" class="btn btn-primary w-100 mb-2"><i class="fas fa-plus me-1"></i> Add event</a>
                <a href="manage_events.php" class="btn btn-outline-secondary w-100 btn-sm">Manage all events</a>
            </div>
        </div>

        <div class="miqt-widget-card">
            <div class="miqt-widget-header"><h6><i class="far fa-clock me-1"></i> Upcoming events</h6></div>
            <div class="miqt-widget-body" style="padding-top:8px;padding-bottom:8px">
                <?php if (empty($upcomingEvents)): ?>
                <p class="small text-muted mb-0">No upcoming events scheduled.</p>
                <?php else: ?>
                <?php foreach ($upcomingEvents as $ue):
                    $dot = miqt_event_dot_class($ue['event_type']);
                    $badge = miqt_event_badge_class($ue['event_type']);
                    $typeLabel = ucfirst(str_replace('_', ' ', $ue['event_type']));
                ?>
                <div class="miqt-event-item">
                    <div class="miqt-event-dot <?php echo htmlspecialchars($dot); ?>"></div>
                    <div class="miqt-event-info flex-grow-1 min-w-0">
                        <div class="miqt-ev-title"><?php echo htmlspecialchars($ue['title']); ?></div>
                        <div class="miqt-ev-meta"><?php echo formatDate($ue['event_date']); ?></div>
                    </div>
                    <span class="miqt-event-badge <?php echo htmlspecialchars($badge); ?>"><?php echo htmlspecialchars($typeLabel); ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
