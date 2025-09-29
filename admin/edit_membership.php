<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_membership.php");
    exit();
}

// Fetch current data
$stmt = $conn->prepare("SELECT m.*, u.first_name, u.last_name, u.email, u.phone 
                        FROM memberships m 
                        JOIN users u ON m.user_id = u.id 
                        WHERE m.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$currentDuration = $data['duration'];
$currentPlan = $data['plan_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = trim($_POST['plan_name']);
    $duration = trim($_POST['duration']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $start_date = date('Y-m-d');

    // Recalculate end_date
    switch ($duration) {
        case '1 Day': $end_date = date('Y-m-d', strtotime("+1 day")); break;
        case '1 Week': $end_date = date('Y-m-d', strtotime("+1 week")); break;
        case '2 Weeks': $end_date = date('Y-m-d', strtotime("+2 weeks")); break;
        case '1 Month': $end_date = date('Y-m-d', strtotime("+1 month")); break;
        case '3 Months': $end_date = date('Y-m-d', strtotime("+3 months")); break;
        case '6 Months': $end_date = date('Y-m-d', strtotime("+6 months")); break;
        case '1 Year': $end_date = date('Y-m-d', strtotime("+1 year")); break;
        default: $end_date = $data['end_date'];
    }

    $stmt = $conn->prepare("UPDATE memberships 
                            SET plan_name=?, duration=?, price=?, description=?, start_date=?, end_date=? 
                            WHERE id=?");
    $stmt->bind_param("ssdsssi", $plan_name, $duration, $price, $description, $start_date, $end_date, $id);
    $stmt->execute();

    header("Location: manage_membership.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Membership</title>
    <style>
    body {
        background: #0d1b2a; /* main dark background */
        color: #ffffff; /* global text color */
        font-family: Arial, sans-serif;
        padding: 40px;
        position: relative;
    }

    .back-arrow {
        position: absolute;
        top: 20px;
        left: 30px;
        font-size: 16px;
        text-decoration: none;
        background-color: #1b263b; /* dark button background */
        color: #ffffff; /* white text */
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .back-arrow:hover {
        background-color: #3b82f6; /* blue highlight */
        color: #ffffff;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 26px;
        font-weight: bold;
        color: #60a5fa; /* accent blue */
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    form {
        max-width: 500px;
        background: #1b263b; /* dark card */
        padding: 20px;
        border-radius: 10px;
        margin: 30px auto 0 auto;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.6);
        color: #ffffff; /* text inside form */
    }

    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #334155; /* subtle dark border */
        background: #0f172a; /* input background */
        color: #f1f5f9; /* input text */
        font-size: 16px;
    }

    option {
        background: #0f172a;
        color: #f1f5f9;
    }

    button {
        background: #3b82f6; /* blue button */
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        color: #ffffff;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #2563eb; /* darker blue on hover */
    }

    label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
        color: #93c5fd; /* lighter blue labels */
    }

    .user-info {
        margin-bottom: 15px;
        padding: 15px;
        background: #1e293b; /* dark panel */
        border: 1px solid #334155;
        border-radius: 8px;
        font-size: 14px;
        color: #f8fafc; /* near-white text */
    }

    .user-info strong {
        color: #60a5fa; /* accent color for titles */
    }

    .user-info span {
        display: block;
        margin-bottom: 5px;
    }
    </style>
</head>
<body>

<a href="manage_membership.php" class="back-arrow">&#8592; </a>

<h2>Edit Membership Plan</h2>

<div class="user-info">
    <strong>Assigned To:</strong>
    <span><strong>Name:</strong> <?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></span>
    <span><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></span>
    <span><strong>Phone:</strong> <?= htmlspecialchars($data['phone']) ?></span>
</div>

<form method="POST">
    <label>Plan Name (Service)</label>
    <select name="plan_name" required>
        <option value="">-- Select Service --</option>
        <option value="Gym Access" <?= $currentPlan == "Gym Access" ? 'selected' : '' ?>>Gym Access</option>
        <option value="Personal Trainer" <?= $currentPlan == "Personal Trainer" ? 'selected' : '' ?>>Personal Trainer</option>
    </select>

    <label>Duration</label>
    <select name="duration" required>
        <option value="">-- Select Duration --</option>
        <option value="1 Day" <?= $currentDuration == "1 Day" ? 'selected' : '' ?>>1 Day</option>
        <option value="1 Week" <?= $currentDuration == "1 Week" ? 'selected' : '' ?>>1 Week</option>
        <option value="2 Weeks" <?= $currentDuration == "2 Weeks" ? 'selected' : '' ?>>2 Weeks</option>
        <option value="1 Month" <?= $currentDuration == "1 Month" ? 'selected' : '' ?>>1 Month</option>
        <option value="3 Months" <?= $currentDuration == "3 Months" ? 'selected' : '' ?>>3 Months</option>
        <option value="6 Months" <?= $currentDuration == "6 Months" ? 'selected' : '' ?>>6 Months</option>
        <option value="1 Year" <?= $currentDuration == "1 Year" ? 'selected' : '' ?>>1 Year</option>
    </select>

    <label>Price (₱)</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($data['price']) ?>" required>

    <button type="submit">Update Membership</button>
</form>

</body>
</html>
