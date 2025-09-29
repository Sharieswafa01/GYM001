<?php
session_start();

// Set PHP timezone to Philippine Standard Time
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Set MySQL timezone to Philippine time
$conn->query("SET time_zone = '+08:00'");

$today = date('Y-m-d');

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

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$total = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Attendance List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #0d1b2a; /* dark navy */
        margin: 0;
        padding: 20px;
        color: #f1f5f9;
    }

    /* Back Button Styling */
    .back-arrow {
        position: absolute;
        top: 20px;
        left: 30px;
        font-size: 16px;
        text-decoration: none;
        background-color: #1b263b;
        color: #f1f5f9;
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .back-arrow:hover {
        background-color: #2d3748;
        color: #3b82f6; /* blue highlight */
    }

    .container {
        background: #1e293b; /* dark slate */
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.4);
        max-width: 1100px;
        margin: auto;
    }

    h2 {
        text-align: center;
        color: #f1f5f9;
        margin-bottom: 20px;
        border-bottom: 2px solid #334155;
        padding-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #1b263b;
        color: #f1f5f9;
        border-radius: 8px;
        overflow: hidden;
    }

    table th, table td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #475569;
    }

    thead {
        background-color: #334155;
    }

    tbody tr:hover {
        background-color: #2d3748; /* row hover effect */
    }

    .status-login {
        color: #22c55e; /* green */
        font-weight: bold;
    }

    .status-logout {
        color: #ef4444; /* red */
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        table th, table td {
            font-size: 14px;
            padding: 10px;
        }
    }
</style>

</head>
<body>

    <!-- Back Button -->
    <a href="manage_users.php" class="back-arrow">&#8592;</a>

    <div class="container">
        <h2>Full Attendance List (<?= htmlspecialchars($today) ?>)</h2>
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
                    $count = $total;
                    while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $count-- ?></td>
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
                    <tr><td colspan="6">No attendance records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
