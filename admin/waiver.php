<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Handle search
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $safeSearch = "%" . $conn->real_escape_string($search) . "%";
    $stmt = $conn->prepare("
        SELECT CONCAT(first_name, ' ', last_name) AS name, email, waiver_signed, waiver_date
        FROM users
        WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)
        ORDER BY waiver_date ASC
    ");
    $stmt->bind_param("sss", $safeSearch, $safeSearch, $safeSearch);
} else {
    $stmt = $conn->prepare("
        SELECT CONCAT(first_name, ' ', last_name) AS name, email, waiver_signed, waiver_date
        FROM users
        ORDER BY waiver_date ASC
    ");
}
$stmt->execute();
$waiver_users = $stmt->get_result();

// CSV export
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="waiver_list.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['No.', 'Name', 'Email', 'Waiver Status']);
    $count = 1;
    while ($row = $waiver_users->fetch_assoc()) {
        $status = $row['waiver_signed'] ? 'Submitted' : 'Not Submitted';
        fputcsv($output, [$count++, $row['name'], $row['email'], $status]);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Waiver Management</title>
<link rel="stylesheet" href="css/all.min.css">
<style>
:root {
    --background-dark: #0d1b2a;
    --sidebar-dark: #1b263b;
    --card-bg: #1e293b;
    --text-light: #f1f5f9;
    --accent-blue: #3b82f6;
    --accent-green: #22c55e;
    --accent-red: #ef4444;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-dark);
    color: var(--text-light);
}

/* Sidebar */
.sidebar {
    width: 290px;
    background: var(--sidebar-dark);
    padding: 20px;
    position: fixed;
    height: 100vh;
    border-right: 1px solid #334155;
}
.sidebar .logo { text-align: center; margin-bottom: 30px; }
.sidebar .logo h2 { margin: 0; font-size: 28px; font-weight: 600; color: var(--accent-blue); }
.sidebar .nav ul { list-style: none; padding: 0; }
.sidebar .nav ul li { margin: 18px 0; }
.sidebar .nav ul li a {
    font-size: 18px; font-weight: 500; color: var(--text-light);
    text-decoration: none; display: flex; align-items: center; gap: 10px;
    padding: 10px 15px; border-radius: 8px; transition: background 0.3s, color 0.3s;
}
.sidebar .nav ul li a:hover, .sidebar .nav ul li a.active {
    background-color: #2d3748; color: var(--accent-blue);
}
.sidebar-logout a {
    display: flex; align-items: center; gap: 10px; margin-top: 60px;
    font-weight: bold; font-size: 18px; color: var(--text-light); text-decoration: none;
    padding: 10px 15px; border-radius: 8px; transition: background 0.3s, color 0.3s;
}
.sidebar-logout a:hover { background: #2d3748; color: var(--accent-red); }

/* Main content */
.main-content {
    margin-left: 310px;
    padding: 50px;
    flex: 1;
}

h1 { font-size: 2.2rem; margin-bottom: 20px; }

/* Search */
.search-container { display: flex; justify-content: flex-end; margin-bottom: 15px; }
.search-box { display: flex; gap: 8px; }
.search-box input {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #334155;
    width: 250px;
    background: var(--card-bg);
    color: var(--text-light);
}
.search-box button {
    padding: 10px 15px;
    background: var(--accent-blue);
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
.search-box button:hover { background: #2563eb; }

/* Table container with download button */
.table-container {
    margin-top: 20px;
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
}
.table-header-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 10px;
}
.download-btn {
    padding: 10px 20px;
    background-color: var(--accent-green);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
}
.download-btn:hover { background-color: #16a34a; }

/* Table */
table { width: 100%; border-collapse: collapse; margin-top: 10px; color: var(--text-light); }
thead { background-color: #334155; }
table th, table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #475569;
}
tr:hover { background-color: #2d3748; }

/* Status labels */
.status-submitted { color: var(--accent-green); font-weight: bold; }
.status-not { color: var(--accent-red); font-weight: bold; }
</style>
</head>
<body>
<div class="sidebar">
    <div class="logo"><h2>GYM ADMIN</h2></div>
    <nav class="nav">
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="attendance_tracking.php"><i class="fas fa-calendar-check"></i> Attendance Tracking</a></li>
            <li><a href="manage_users.php"><i class="fas fa-user-friends"></i> Manage Users</a></li>
            <li><a href="manage_equipment.php"><i class="fas fa-dumbbell"></i> Equipment</a></li>
            <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <li><a href="manage_membership.php"><i class="fas fa-credit-card"></i> Membership</a></li>
            <li><a class="active" href="waiver.php"><i class="fas fa-file-signature"></i> Waiver</a></li>
        </ul>
    </nav>
    <div class="sidebar-logout">
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <h1><i class="fas fa-file-signature"></i> Waiver Management</h1>
    <p>View all gym members and students who have clicked the waiver checkbox.</p>

    <!-- Search -->
    <div class="search-container">
        <form method="get" class="search-box">
            <input type="text" name="search" placeholder="Search by name or email"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>

    <div class="table-container">
        <div class="table-header-actions">
            <a href="?download=csv" class="download-btn"><i class="fas fa-download"></i> Download All Users</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Waiver Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                $waiver_users->data_seek(0);
                while ($row = $waiver_users->fetch_assoc()):
                    $status = $row['waiver_signed']
                        ? '<span class="status-submitted">✅ Submitted</span>'
                        : '<span class="status-not">❌ Not Submitted</span>';
                ?>
                <tr>
                    <td><?= $count++; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= $status; ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if ($count === 1): ?>
                <tr><td colspan="4" style="text-align:center;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>



