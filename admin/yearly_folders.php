<?php
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');
$conn->query("SET time_zone = '+08:00'");

// Get distinct years from attendance table
$query = "SELECT DISTINCT YEAR(timestamp) AS year FROM attendance ORDER BY year DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yearly Attendance Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
        }
        .top-bar {
            padding: 15px 20px;
        }
        .back-btn {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 8px 14px;
            display: inline-block;
            text-decoration: none;
            color: #333;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: background-color 0.2s ease;
        }
        .back-btn:hover {
            background-color:  #00ff99;
        }
        .container {
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background-color: #fff6f6;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            max-width: 800px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .year-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }
        .year-item {
            background-color:rgb(68, 159, 68); 
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
            color: #004085;
            text-decoration: none;
            display: block;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .year-item:hover {
            background-color: #66cc66; 
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="attendance_tracking.php" class="back-btn">←</a>
</div>

<div class="container">
    <div class="card">
        <h2>Yearly Attendance Records</h2>
        <div class="year-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <a class="year-item" href="monthly_folders.php?year=<?= urlencode($row['year']) ?>">
                        <?= htmlspecialchars($row['year']) ?>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">No yearly attendance records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
