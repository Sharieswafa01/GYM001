<?php
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Retrieve month parameter from URL
$monthParam = $_GET['month'] ?? '';
if (!$monthParam) {
    echo "Invalid month selected.";
    exit();
}

// Convert month string like "May 2025" into a date range
$timestamp = strtotime($monthParam);
if (!$timestamp) {
    echo "Invalid month format.";
    exit();
}

$startDate = date('Y-m-01 00:00:00', $timestamp);
$endDate = date('Y-m-t 23:59:59', $timestamp);

// Fetch attendance data for the selected month
$query = "
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) AS full_name,
        u.role,
        a.status,
        a.login_time,
        a.logout_time
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE a.timestamp BETWEEN '$startDate' AND '$endDate'
    ORDER BY a.timestamp ASC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($monthParam) ?> Attendance</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .back-arrow {
            text-decoration: none;
            font-size: 18px;
            color: #007bff;
        }

        .back-arrow:hover {
            text-decoration: underline;
        }

        p {
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>

 <a href="monthly_folders.php" class="back-arrow">&#8592;</a>
<div class="container">
    <h2><?= htmlspecialchars($monthParam) ?> - Attendance Records</h2>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Login Time</th>
                <th>Logout Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['login_time'] ? htmlspecialchars($row['login_time']) : '—' ?></td>
                        <td><?= $row['logout_time'] ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No records found for this month.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>


