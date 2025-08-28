<?php
include('../user/db_connection.php');

// Set timezone
date_default_timezone_set('Asia/Manila');

// Use previous month
$month = date('n') - 1;
$year = date('Y');

// If it's January, go to December last year
if ($month === 0) {
    $month = 12;
    $year--;
}

// Get number of days in that month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Fetch all users
$userQuery = $conn->query("SELECT id FROM users");

while ($user = $userQuery->fetch_assoc()) {
    $userId = $user['id'];
    $presentDays = 0;

    // Loop through each day of the month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE user_id = ? AND DATE(timestamp) = ?");
        $stmt->bind_param('is', $userId, $date);
        $stmt->execute();
        $stmt->bind_result($attendanceCount);
        $stmt->fetch();
        $stmt->close();

        if ($attendanceCount > 0) {
            $presentDays++;
        }
    }

    $absentDays = $daysInMonth - $presentDays;

    // Insert or update monthly summary
    $insertStmt = $conn->prepare("
        INSERT INTO monthly_attendance (user_id, year, month, days_present, days_absent)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE days_present = ?, days_absent = ?
    ");
    $insertStmt->bind_param('iiiiiii', $userId, $year, $month, $presentDays, $absentDays, $presentDays, $absentDays);
    $insertStmt->execute();
    $insertStmt->close();
}

echo "✅ Monthly attendance archived for $month/$year.";
