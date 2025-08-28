<?php
session_start();

// Set timezone to Philippine Standard Time
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Optional: Set MySQL timezone to Asia/Manila as well
$conn->query("SET time_zone = '+08:00'");

$today = date('Y-m-d');
$viewAll = isset($_GET['view']) && $_GET['view'] === 'all';

$query = "
    SELECT 
        a.id, 
        CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
        u.role, 
        a.status, 
        a.login_time, 
        a.logout_time 
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE DATE(a.timestamp) = ?
    ORDER BY a.timestamp DESC
";

if (!$viewAll) {
    $query .= " LIMIT 5";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

// Get total records count
$countQuery = "SELECT COUNT(*) as total FROM attendance WHERE DATE(timestamp) = ?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("s", $today);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; }
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        .dashboard-wrapper {
            display: flex;
            height: 100vh;
        }

         .sidebar {
        width: 290px;
        background: #111; /* your original background color */
        backdrop-filter: blur(6px);
        color: #fff;
        padding: 20px 20px 40px;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar .logo {
        text-align: center;
        padding: 30px 0 20px;
        border-bottom: 1px solid #444;
        margin-bottom: 30px;
    }

    .sidebar .logo h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        letter-spacing: 1.5px;
        color: #fff;
    }

    .sidebar .nav ul {
        list-style: none;
        padding: 0;
    }

    .sidebar .nav ul li {
        margin: 18px 0;
    }

    .sidebar .nav ul li a {
        font-size: 18px;
        font-weight: 500;
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-radius: 6px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar .nav ul li a:hover {
        background-color: #333;
        color: #4caf50;
    }

    .sidebar-logout a {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 60px;
        font-weight: bold;
        font-size: 18px;
        padding: 10px 15px;
        border-radius: 6px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar-logout a:hover {
        background-color: #333;
        color: #f44336;
    }

        .main-content {
            margin-left: 290px;
            padding: 40px;
            flex: 1;
            overflow-y: auto;
        }

        .attendance-tracking {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .attendance-tracking h2 {
            font-size: 1.6rem;
            margin-bottom: 20px;
            color: #222;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9f9f9;
            margin-bottom: 10px;
        }

        thead {
            background-color: #ddd;
        }

        table th, table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        .status-login {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-logout {
            color: #F44336;
            font-weight: bold;
        }

        .link-right {
            text-align: right;
            margin-top: 10px;
        }

        .link-right a {
            font-weight: bold;
            text-decoration: none;
            color: #007bff;
        }

        .link-right a:hover {
            text-decoration: underline;
        }

        .folder-section {
            margin-top: 40px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        .folder-section h3 a {
            text-decoration: none;
            color: #111;
            font-weight: bold;
        }

        .folder-section h3 a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .dashboard-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>Gym Admin</h2>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="attendance_tracking.php"><i class="fas fa-calendar-check"></i> Attendance Tracking</a></li>
                <li><a href="manage_users.php"><i class="fas fa-user-friends"></i> Manage Users</a></li>
                <li><a href="manage_equipment.php"><i class="fas fa-dumbbell"></i> Equipment</a></li>
                <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="manage_membership.php"><i class="fas fa-credit-card"></i> Membership</a></li>
            </ul>
        </nav>
        <div class="sidebar-logout">
            <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <section class="attendance-tracking">
            <h2>Today's Attendance (<?= htmlspecialchars($today) ?>)</h2>
            <table>
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
                    <?php 
                    if ($result && $result->num_rows > 0): 
                        $start = $totalRecords;
                        while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $start-- ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td class="<?= $row['status'] === 'Login' ? 'status-login' : 'status-logout' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </td>
                                <td><?= $row['login_time'] ? htmlspecialchars($row['login_time']) : '—' ?></td>
                                <td><?= $row['logout_time'] ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                            </tr>
                    <?php endwhile;
                    else: ?>
                        <tr><td colspan="6">No attendance records for today.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="link-right">
                <a href="view_full_attendance.php">View Full List (<?= $totalRecords ?> records)</a>
            </div>
        </section>

        <div class="folder-section">
            <h3><a href="daily_folders.php"><i class="fas fa-folder"></i> Daily Attendance Records</a></h3>
        </div>
        <div class="folder-section">
            <h3><a href="monthly_folders.php"><i class="fas fa-folder"></i> Monthly Attendance Records</a></h3>
        </div>
        <div class="folder-section">
            <h3><a href="yearly_folders.php"><i class="fas fa-folder"></i> Yearly Attendance Records</a></h3>
        </div>
    </div>
</div>
</body>
</html>
