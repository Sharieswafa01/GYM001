<?php
// ==============================
// admin_dashboard.php (SECURE)
// ==============================

// --- Secure session cookie params (IMPORTANT: set BEFORE session_start())
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
$httponly = true;
$samesite = 'Lax'; // 'Strict' or 'Lax' depending on needs

// PHP < 7.3 compatibility: session_set_cookie_params accepts 7 args only on newer versions.
// Use array option if available, otherwise fallback.
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
} else {
    // fallback for older PHP versions (samesite not settable here)
    session_set_cookie_params(0, '/', $_SERVER['HTTP_HOST'] ?? '', $secure, $httponly);
}

session_start();

// Prevent caching so browser won't show protected pages after logout
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// -----------------------------
// Session security policies
// -----------------------------

// If not logged in -> redirect to login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Regenerate session id periodically to mitigate fixation
// if (!isset($_SESSION['regenerated_time']) || time() - $_SESSION['regenerated_time'] > 300) { // every 5 minutes
//     session_regenerate_id(true);
//     $_SESSION['regenerated_time'] = time();
// }

// Disable auto-logout timer — admin stays logged in until logout is clicked
$_SESSION['last_activity'] = time(); // still track activity for reference, but no forced logout


// Optional: Bind session to IP and User-Agent to make hijacking harder
// Enable if your users have stable IPs / UA patterns. Might break legitimate users behind rotating proxies.
// if (!isset($_SESSION['fingerprint'])) {
//     $_SESSION['fingerprint'] = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
// } else {
//     $currentFingerprint = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
//     if (!hash_equals($_SESSION['fingerprint'], $currentFingerprint)) {
//         session_unset();
//         session_destroy();
//         header("Location: admin_login.php");
//         exit();
//     }
// }

// -----------------------------
// Database connection
// -----------------------------
include('../user/db_connection.php'); // Ensure this file sets $conn = new mysqli(...)

// Basic connection check
if (!isset($conn) || $conn->connect_error) {
    // In production avoid revealing DB errors. Use generic message.
    die("Database connection not established. Please try again later.");
}

// -----------------------------
// Fetch admin details securely
// -----------------------------
$adminPhoto = 'default_user.png';
$adminEmail = '';
$adminId = intval($_SESSION['admin_id']);

if ($adminId > 0) {
    $adminStmt = $conn->prepare("SELECT profile_picture, email FROM admin WHERE admin_id = ?");
    if ($adminStmt) {
        $adminStmt->bind_param("i", $adminId);
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result();
        if ($ad = $adminResult->fetch_assoc()) {
            $adminEmail = $ad['email'] ?? '';
            // sanitize filename
            $photoFile = trim($ad['profile_picture'] ?? '');
            if ($photoFile !== '') {
                // Basic sanitization - allow only safe filename chars
                $photoFile = basename($photoFile);
                $uploadsPath = '../uploads/admins/';
                $fullPath = realpath($uploadsPath . $photoFile);
                // ensure that file exists and is inside uploads folder
                if ($fullPath && strpos($fullPath, realpath($uploadsPath)) === 0 && is_file($fullPath)) {
                    $adminPhoto = $uploadsPath . $photoFile;
                }
            }
        }
        $adminStmt->close();
    }
}

// -----------------------------
// Notification counts
// -----------------------------
// New users in last 24 hours (prepared not strictly necessary, but consistent)
$newUsersCount = 0;
if ($stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM users WHERE created_at >= (NOW() - INTERVAL 1 DAY)")) {
    $stmt->execute();
    $stmt->bind_result($cnt_new_users);
    if ($stmt->fetch()) $newUsersCount = intval($cnt_new_users);
    $stmt->close();
}

// Expiring memberships within next 1 day (and not expired yet)
$expiringCount = 0;
$expQuery = "
    SELECT COUNT(*) AS cnt
    FROM memberships m
    JOIN users u ON u.id = m.user_id
    WHERE DATE_ADD(m.start_date, INTERVAL m.duration DAY) <= (NOW() + INTERVAL 1 DAY)
      AND DATE_ADD(m.start_date, INTERVAL m.duration DAY) > NOW()
";
if ($stmt = $conn->prepare($expQuery)) {
    $stmt->execute();
    $stmt->bind_result($cnt_exp);
    if ($stmt->fetch()) $expiringCount = intval($cnt_exp);
    $stmt->close();
}

$notificationCount = $newUsersCount + $expiringCount;

// -----------------------------
// Dashboard counts (safe)
$userCount = 0;
if ($r = $conn->query("SELECT COUNT(*) as total_users FROM users")) {
    $userCount = intval($r->fetch_assoc()['total_users'] ?? 0);
}

$equipmentCount = 0;
if ($r = $conn->query("SELECT COUNT(*) as total_equipment FROM equipment")) {
    $equipmentCount = intval($r->fetch_assoc()['total_equipment'] ?? 0);
}

$membershipCount = 0;
if ($r = $conn->query("SELECT COUNT(*) as total_memberships FROM memberships")) {
    $membershipCount = intval($r->fetch_assoc()['total_memberships'] ?? 0);
}

// Active now: today's attendance with status 'Login' and logout_time IS NULL
$activeNow = 0;
if ($stmt = $conn->prepare("SELECT COUNT(*) AS active_now FROM attendance WHERE DATE(timestamp) = CURDATE() AND status = 'Login' AND (logout_time IS NULL OR logout_time = '')")) {
    $stmt->execute();
    $stmt->bind_result($cnt_active_now);
    if ($stmt->fetch()) $activeNow = intval($cnt_active_now);
    $stmt->close();
}

// Total today (for numbering)
$totalToday = 0;
if ($stmt = $conn->prepare("SELECT COUNT(*) AS total_today FROM attendance WHERE DATE(timestamp) = CURDATE()")) {
    $stmt->execute();
    $stmt->bind_result($cnt_today);
    if ($stmt->fetch()) $totalToday = intval($cnt_today);
    $stmt->close();
}

// Recent attendance (LIMIT 5)
$rows = [];
$recentQuery = "
    SELECT 
        a.id, 
        CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
        u.role, 
        a.status, 
        a.login_time, 
        a.logout_time 
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE DATE(a.timestamp) = CURDATE()
    ORDER BY a.timestamp DESC
    LIMIT 5
";
if ($res = $conn->query($recentQuery)) {
    $rows = $res->fetch_all(MYSQLI_ASSOC);
}

// Close DB connection at end of script? We'll leave it to PHP to close, but you can close explicitly:
// $conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all.min.css">
   <style>
    /* ========== Updated Dark Theme ========== */
    /* ========== Updated Dark Theme ========== */
:root {
    --background-dark: #0d1b2a;   /* deep navy */
    --sidebar-dark: #1b263b;      /* sidebar navy */
    --card-bg: #1e293b;           /* slate blue-gray */
    --text-light: #f1f5f9;        /* near white */
    --text-muted: #94a3b8;        /* muted gray-blue */
    --accent-blue: #3b82f6;       /* bright cyan/blue */
    --accent-green: #22c55e;      /* modern green */
    --accent-red: #ef4444;        /* alert red */
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-dark);
    color: var(--text-light);
}

.dashboard-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

/* Sidebar */
.sidebar {
    width: 290px;
    background: var(--sidebar-dark);
    backdrop-filter: blur(6px);
    color: var(--text-light);
    padding: 20px 20px 40px;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    border-right: 1px solid #334155; /* subtle divider */
}

.sidebar .logo {
    text-align: center;
    padding: 30px 0 20px;
    border-bottom: 1px solid #334155;
    margin-bottom: 30px;
}

.sidebar .logo h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    color: var(--accent-blue);
}

.sidebar .nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar .nav ul li {
    margin: 18px 0;
}

.sidebar .nav ul li a {
    font-size: 18px;
    font-weight: 500;
    color: var(--text-light);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: background 0.3s, color 0.3s;
}

.sidebar .nav ul li a:hover {
    background-color: #2d3748;
    color: var(--accent-blue);
}

.sidebar-logout a {
    color: var(--text-light);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 60px;
    font-weight: bold;
    font-size: 18px;
    padding: 10px 15px;
    border-radius: 8px;
    transition: background 0.3s, color 0.3s;
}

.sidebar-logout a:hover {
    background-color: #2d3748;
    color: var(--accent-red);
}

/* Active link */
.sidebar .nav ul li a.active {
    background-color: #2d3748;
    color: var(--accent-blue);
    font-weight: 600;
}

/* Main content */
.main-content {
    margin-left: 310px;   /* space from sidebar */
    padding: 50px;
    flex: 1;
    overflow-y: auto;
}

h1 {
    font-size: 2.2rem;
    margin-bottom: 20px;
    color: var(--text-light);
}

/* Dashboard cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-bottom: 50px;
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card {
    background-color: var(--card-bg);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 160px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 30px rgba(0,0,0,0.6);
}

.card i {
    font-size: 40px;
    margin-bottom: 10px;
    color: var(--accent-blue);
}

.card h3 {
    margin: 10px 0 5px;
    font-size: 20px;
    color: var(--text-light);
}

.card .count {
    font-size: 24px;
    font-weight: bold;
    color: var(--accent-blue);
}

/* Attendance section */
.attendance-tracking {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
}

.attendance-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.attendance-header h2 {
    font-size: 1.6rem;
    color: var(--text-light);
    margin: 0;
}

.view-all {
    font-size: 14px;
    text-decoration: none;
    color: var(--accent-blue);
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--sidebar-dark);
    margin-top: 15px;
    color: var(--text-light);
}

thead {
    background-color: #334155;
}

table th, table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #475569;
}

.status-login {
    color: var(--accent-green);
    font-weight: bold;
}

.status-logout {
    color: var(--accent-red);
    font-weight: bold;
}

/* Top icons */
.top-icons {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 20px;
}

.notification-icon {
    position: relative; /* ensure badge sticks to bell */
    display: inline-block;
}

.notification-icon i {
    font-size: 22px;
    color: var(--text-light);
}

.notification-icon:hover i {
    color: var(--accent-blue);
}

.profile-pic img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--accent-blue);
}

.badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: var(--accent-red);
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
    line-height: 1;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .dashboard-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-right: none;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }

    .dashboard-cards {
        grid-template-columns: 1fr;
    }

    .top-icons {
        position: static;
        justify-content: flex-end;
        margin: 10px 0;
    }
}

</style>


</head>
<body>

<!-- notification sound -->
<audio id="notifSound" src="../assets/notification.mp3" preload="auto"></audio>

<!-- Top icons -->
<div class="top-icons" aria-hidden="false">
    <a href="notifications.php" class="notification-icon" id="notifBell" title="Notifications" aria-label="Notifications">
        <i class="fas fa-bell" aria-hidden="true"></i>
        <?php if ($notificationCount > 0): ?>
            <span class="badge" id="notifBadge"><?= intval($notificationCount) ?></span>
        <?php endif; ?>
    </a>
    <a href="admin_profile.php" class="profile-pic" title="Admin Profile" aria-label="Admin Profile">
        <img src="<?= htmlspecialchars($adminPhoto) ?>" alt="Admin profile picture">
    </a>
</div>


<script>
/* Notification polling
   - Polls fetch_notifications.php which must return JSON: { "total": <number> }
   - Plays sound and blinks bell if new notifications appear.
   - Avoids injecting HTML from server — treats data as numbers only.
*/
(function() {
    let currentCount = <?= intval($notificationCount) ?>;
    const notifSound = document.getElementById('notifSound');
    const notifBell = document.getElementById('notifBell');

    async function checkNotifications() {
        try {
            const res = await fetch('fetch_notifications.php', { method: 'GET', credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            const newCount = Number(data.total) || 0;
            const badge = document.getElementById('notifBadge');

            if (newCount > currentCount) {
                try { notifSound.play().catch(()=>{}); } catch(e) {}
                notifBell.classList.add('blinking');
            }

            if (newCount > 0) {
                if (badge) {
                    badge.textContent = newCount;
                } else {
                    const s = document.createElement('span');
                    s.className = 'badge';
                    s.id = 'notifBadge';
                    s.textContent = newCount;
                    notifBell.appendChild(s);
                }
            } else {
                notifBell.classList.remove('blinking');
                if (badge) badge.remove();
            }

            currentCount = newCount;
        } catch (err) {
            // silent fail: network error or invalid JSON
            console.warn('Notification check failed', err);
        }
    }

    // reset count when clicking bell
    notifBell.addEventListener('click', () => {
        currentCount = 0;
        fetch('reset_notification_count.php', { method: 'POST', credentials: 'same-origin' }).catch(()=>{});
        document.getElementById('notifBadge')?.remove();
        notifBell.classList.remove('blinking');
    });

    // poll every 10 seconds
    setInterval(checkNotifications, 10000);
    // initial check (optional)
    // setTimeout(checkNotifications, 1000);
})();
</script>

<div class="dashboard-wrapper">
    <aside class="sidebar" role="navigation" aria-label="Sidebar Navigation">
        <div class="logo"><h2>GYM ADMIN</h2></div>
        <nav class="nav" role="menu">
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line" aria-hidden="true"></i> Dashboard</a></li>
                <li><a href="attendance_tracking.php"><i class="fas fa-calendar-check" aria-hidden="true"></i> Attendance Tracking</a></li>
                <li><a href="manage_users.php"><i class="fas fa-user-friends" aria-hidden="true"></i> Manage Users</a></li>
                <li><a href="manage_equipment.php"><i class="fas fa-dumbbell" aria-hidden="true"></i> Equipment</a></li>
                <li><a href="announcement.php"><i class="fas fa-bullhorn" aria-hidden="true"></i> Announcements</a></li>
                <li><a href="manage_membership.php"><i class="fas fa-credit-card" aria-hidden="true"></i> Membership</a></li>
                <li><a href="waiver.php"><i class="fas fa-file-signature" aria-hidden="true"></i> Waiver</a></li>
            </ul>
        </nav>

        <div class="sidebar-logout">
            <!-- make sure admin_logout.php is the improved secure logout (clears cookies, cache headers) -->
            <a href="admin_logout.php"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout</a>
        </div>
    </aside>

    <div class="main-content" role="main">
        <h1>Welcome to Dashboard</h1>

        <section class="dashboard-cards" aria-label="Dashboard summary">
            <a class="card-link" href="attendance_tracking.php">
                <div class="card" role="button" tabindex="0">
                    <i class="fas fa-user-check" aria-hidden="true"></i>
                    <h3>Active Today</h3>
                    <p>Total Active: <span class="count"><?= intval($activeNow) ?></span></p>
                </div>
            </a>

            <a class="card-link" href="manage_users.php">
                <div class="card" role="button" tabindex="0">
                    <i class="fas fa-user-friends" aria-hidden="true"></i>
                    <h3>Users</h3>
                    <p>Total Users: <span class="count"><?= intval($userCount) ?></span></p>
                </div>
            </a>

            <a class="card-link" href="manage_equipment.php">
                <div class="card" role="button" tabindex="0">
                    <i class="fas fa-dumbbell" aria-hidden="true"></i>
                    <h3>Equipment</h3>
                    <p>Total Equipment: <span class="count"><?= intval($equipmentCount) ?></span></p>
                </div>
            </a>

            <a class="card-link" href="manage_membership.php">
                <div class="card" role="button" tabindex="0">
                    <i class="fas fa-credit-card" aria-hidden="true"></i>
                    <h3>Membership</h3>
                    <p>Total Plans: <span class="count"><?= intval($membershipCount) ?></span></p>

                </div>
            </a>
        </section>

        <section class="attendance-tracking" aria-label="Today's attendance">
            <div class="attendance-header">
                <h2>Today's Attendance Records</h2>
                <a href="view_full_attendance.php" class="view-all">View All</a>
            </div>

            <table aria-describedby="attendance-desc">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) > 0): ?>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr>
                                <td><?= max(0, intval($totalToday) - intval($index)) ?></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['role'] ?? '—') ?></td>
                                <td class="<?= (isset($row['status']) && $row['status'] === 'Login') ? 'status-login' : 'status-logout' ?>">
                                    <?= htmlspecialchars($row['status'] ?? '—') ?>
                                </td>
                                <td><?= !empty($row['login_time']) ? htmlspecialchars($row['login_time']) : '—' ?></td>
                                <td><?= !empty($row['logout_time']) ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No attendance records found for today.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

</body>
</html>
