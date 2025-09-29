<?php
session_start();
include('../user/db_connection.php'); // Adjusted DB path

if (!$conn) {
    die("Database connection not established.");
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$current_year = date("Y");

// Get all months (January - December)
$months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Attendance Records</title>
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0d1b2a; /* Dark navy */
            color: #e0e6ed; /* Light text */
            margin: 0;
            padding: 0;
        }
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 16px;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.08);
            color: #e0e6ed;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .back-arrow:hover {
            background-color: #1b263b;
            color: #00b4d8; /* Cyan highlight */
        }
        .container {
            background-color: #1b263b;
            padding: 30px;
            margin: 50px auto;
            border-radius: 12px;
            width: 95%;
            max-width: 1100px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }
        h2 {
            margin-bottom: 30px;
            color: #90e0ef; /* Light cyan accent */
            text-align: center;
        }
        .month-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }
        /* Full-card clickable link */
        .month-card {
            display: block;
            background: #90e0ef; /* Light blue */
            border-radius: 10px;
            padding: 25px 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, background-color 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: #0d1b2a; /* Dark navy text */
            font-size: 18px;
            font-weight: bold;
        }
        .month-card:hover {
            background-color: #0077b6; /* Dark blue */
            color: #ffffff;
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }
        @media (max-width: 600px) {
            .month-card {
                padding: 20px 10px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <a href="attendance_tracking.php" class="back-arrow">&#8592;</a>
    <div class="container">
        <h2>Monthly Attendance Records - <?= htmlspecialchars($current_year) ?></h2>
        <div class="month-grid">
            <?php foreach ($months as $index => $month): ?>
                <a class="month-card" href="daily_folders.php?month=<?= $index + 1 ?>&year=<?= $current_year ?>">
                    <?= htmlspecialchars($month) ?> <?= $current_year ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

