<?php
session_start();
include('../user/db_connection.php');

if (!$conn) {
    die("Database connection not established.");
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get month/year from URL
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get month name
$month_name = date("F", mktime(0, 0, 0, $selected_month, 1));

// Get number of days in month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);

// Fetch attendance for that month/year
$sql = "SELECT DAY(login_time) AS day, COUNT(*) AS total_records
        FROM attendance
        WHERE MONTH(login_time) = ? AND YEAR(login_time) = ?
        GROUP BY DAY(login_time)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $selected_month, $selected_year);
$stmt->execute();
$result = $stmt->get_result();

// Store attendance in array
$attendance_data = [];
while ($row = $result->fetch_assoc()) {
    $attendance_data[intval($row['day'])] = intval($row['total_records']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($month_name) ?> <?= $selected_year ?> - Daily Attendance</title>
    <style>
        body {
    font-family: Arial, sans-serif;
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
    text-align: center;
    margin-bottom: 30px;
    color: #90e0ef; /* Light cyan accent */
}
.days-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
}
/* Full-card clickable */
.day-card {
    display: block;
    background: #90e0ef; /* Light blue default */
    border-radius: 8px;
    padding: 20px 10px;
    text-align: center;
    transition: transform 0.2s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    color: #0d1b2a; /* Dark text */
    font-weight: bold;
}
.day-card:hover {
    transform: translateY(-5px);
    background-color: #0077b6; /* Dark blue on hover */
    color: #ffffff;
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
}
.day-card small {
    color: #073b4c; /* Dark teal for record text */
    font-weight: normal;
}
.day-card.empty {
    background: #2c2f38; /* Muted gray for empty days */
    color: #aaa;
    pointer-events: none; /* Non-clickable */
}
.day-card.empty small {
    color: #f1f1f1; /* Light color for 'No records' text only */
}


    </style>
</head>
<body>
    <a href="monthly_folders.php" class="back-arrow">&#8592;</a>
    <div class="container">
        <h2><?= htmlspecialchars($month_name) ?> <?= $selected_year ?> - Daily Attendance</h2>
        <div class="days-grid">
            <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                <?php if (isset($attendance_data[$day])): ?>
                    <a class="day-card" href="view_attendance.php?day=<?= $day ?>&month=<?= $selected_month ?>&year=<?= $selected_year ?>">
                        Day <?= $day ?><br>
                        <small><?= $attendance_data[$day] ?> record(s)</small>
                    </a>
                <?php else: ?>
                    <div class="day-card empty">
                        Day <?= $day ?><br>
                        <small>No records</small>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
