<?php 
session_start();
include('../user/db_connection.php');

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle and sanitize date
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = date("Y-m-d"); // fallback to today's date
}

// Fetch attendance for the specific day
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
    WHERE DATE(a.login_time) = ?
    ORDER BY a.login_time DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all results into an array for custom numbering
$rows = $result->fetch_all(MYSQLI_ASSOC);
$total_rows = count($rows);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance for <?= date("F j, Y", strtotime($date)) ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 40px auto;
            max-width: 1000px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px; /* Fixed to left side */
            font-size: 16px;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-arrow:hover {
            background-color: rgb(1, 255, 153);
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: rgb(0, 0, 0);
            color: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .status-login {
            color: green;
            font-weight: bold;
        }

        .status-logout {
            color: red;
            font-weight: bold;
        }

        .no-records {
            text-align: center;
            padding: 15px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <a href="daily_folders.php" class="back-arrow" title="Go back">&#8592;</a>
    <div class="container">
        <h2>Attendance for <?= date("F j, Y", strtotime($date)) ?></h2>

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
                <?php if ($total_rows > 0): ?>
                    <?php 
                    // Loop through rows and assign No. so that bottom row is 1
                    for ($i = 0; $i < $total_rows; $i++): 
                        $row = $rows[$i];
                        $no = $total_rows - $i;  // Reverse numbering
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td class="<?= $row['status'] === 'Login' ? 'status-login' : 'status-logout' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                            <td><?= $row['login_time'] ? htmlspecialchars($row['login_time']) : '—' ?></td>
                            <td><?= $row['logout_time'] ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">No attendance records found for this date.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


