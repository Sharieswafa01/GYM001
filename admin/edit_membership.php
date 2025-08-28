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
            background: #ffffff;
            color: #000000;
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
            background-color: #00ff99;
            color: #000;
        }

        form {
            max-width: 500px;
            background: #f7f7f7;
            padding: 20px;
            border-radius: 10px;
            margin: 80px auto 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            background: #ffffff;
            color: #000000;
            font-size: 16px;
        }

        option {
            color: #000000;
        }

        button {
            background: #00ff99;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            color: #000000;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #00e68a;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .user-info {
            margin-bottom: 15px;
            padding: 15px;
            background: #e7f4e8;
            border: 1px solid #b7dcb8;
            border-radius: 8px;
            font-size: 14px;
        }

        .user-info span {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<a href="manage_membership.php" class="back-arrow">&#8592; </a>

<h2 style="text-align:center;">Edit Membership Plan</h2>

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
 