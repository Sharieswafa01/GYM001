<?php
session_start();
include('../user/db_connection.php');
date_default_timezone_set('Asia/Manila');

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Read and normalize inputs
$day = isset($_GET['day']) ? (int)$_GET['day'] : 0;
$month_input = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$from = isset($_GET['from']) ? $_GET['from'] : ''; // to know where back button goes

// Convert month input to month number (1-12)
$month_number = 0;
if ($month_input === null) {
    $month_number = (int)date('n');
} elseif (is_numeric($month_input)) {
    $month_number = (int)$month_input;
} else {
    $months = [
        'january'=>1,'february'=>2,'march'=>3,'april'=>4,'may'=>5,'june'=>6,
        'july'=>7,'august'=>8,'september'=>9,'october'=>10,'november'=>11,'december'=>12
    ];
    $lower = strtolower($month_input);
    if (isset($months[$lower])) {
        $month_number = $months[$lower];
    }
}

// Validate the date
$valid_date = false;
if ($month_number >= 1 && $month_number <= 12 && $day >= 1 && $year >= 1970) {
    if (checkdate($month_number, $day, $year)) {
        $valid_date = true;
    }
}

// Decide back link based on "from"
if ($from === 'daily') {
    $back_link = 'attendance_tracking.php?month=' . $month_number . '&year=' . $year;
} else {
    $back_link = 'daily_folders.php?month=' . $month_number . '&year=' . $year;
}

// If invalid date
if (!$valid_date) {
    echo <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invalid date</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; padding:40px; }
        .card { background:white; padding:20px; border-radius:8px; max-width:700px; margin:0 auto; box-shadow:0 6px 18px rgba(0,0,0,0.08);}
        .back { display:inline-block; margin-bottom:12px; text-decoration:none; color:#000; background:#eee; padding:8px 12px; border-radius:6px; }
        h1 { margin:0 0 10px 0; font-size:20px; }
    </style>
</head>
<body>
    <div class="card">
        <a class="back" href="{$back_link}">&larr; Back</a>
        <h1>Invalid date</h1>
        <p>The date you requested is invalid. Please return to the calendar and try again.</p>
    </div>
</body>
</html>
HTML;
    exit();
}

// Build YYYY-MM-DD
$date_iso = sprintf('%04d-%02d-%02d', $year, $month_number, $day);

// Query attendance for that date
$sql = "
    SELECT 
        a.id,
        a.user_id,
        CONCAT(u.first_name, ' ', u.last_name) AS full_name,
        COALESCE(u.role, '') AS role,
        a.status,
        a.login_time,
        a.logout_time
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE DATE(a.login_time) = ?
    ORDER BY a.login_time DESC
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param('s', $date_iso);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$total_rows = count($rows);

$month_name = date("F", mktime(0,0,0,$month_number,1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance for <?= htmlspecialchars(date("F j, Y", strtotime($date_iso))) ?></title>
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #0d1b2a; /* Dark navy */
        margin: 0;
        padding: 0;
        color: #e0e6ed; /* Light text */
    }
    .container {
        max-width: 1100px;
        margin: 40px auto;
        background: #1b263b; /* Dark panel */
        padding: 28px;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
    }
    .back-arrow {
        position: fixed;
        top: 20px;
        left: 30px;
        background: rgba(255, 255, 255, 0.08);
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        color: #e0e6ed;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
    }
    .back-arrow:hover {
        background: #1b263b;
        color: #00b4d8; /* Cyan highlight */
    }
    h2 {
        text-align: center;
        margin: 0 0 18px 0;
        color: #90e0ef; /* Light cyan accent */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
        background: #14213d; /* Darker card look */
        border-radius: 6px;
        overflow: hidden;
    }
    thead {
        background: #0077b6; /* Bright blue header */
        color: #ffffff;
    }
    th, td {
        padding: 12px 14px;
        border: 1px solid #2c2f38; /* Subtle dark border */
        text-align: center;
    }
    tbody tr:nth-child(even) {
        background: #1f2d3d; /* Alternating row color */
    }
    tbody tr:nth-child(odd) {
        background: #22313f;
    }
    .status-login {
        color: #4cc9f0; /* Cyan for login */
        font-weight: 700;
    }
    .status-logout {
        color: #ff6b6b; /* Soft red for logout */
        font-weight: 700;
    }
    .no-records {
        text-align: center;
        padding: 18px;
        color: #8899aa;
        font-style: italic;
    }
</style>

</head>
<body>
    <a class="back-arrow" href="<?= htmlspecialchars($back_link) ?>">&larr;</a>
    <div class="container">
        <h2>Attendance for <?= htmlspecialchars(date("F j, Y", strtotime($date_iso))) ?></h2>

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
                    <?php for ($i = 0; $i < $total_rows; $i++): 
                        $row = $rows[$i];
                        $no = $total_rows - $i;
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
<?php
$stmt->close();
$conn->close();
?>


