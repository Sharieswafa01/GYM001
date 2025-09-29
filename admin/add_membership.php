<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('../user/db_connection.php');

// ✅ Only fetch customers who do NOT already have a membership
$user_query = "
    SELECT u.id, u.first_name, u.last_name, u.email, u.phone
    FROM users u
    WHERE u.role = 'Customer'
      AND u.id NOT IN (SELECT user_id FROM memberships)
    ORDER BY u.first_name
";
$users_result = $conn->query($user_query);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $services = trim($_POST['services']);
    $duration = trim($_POST['duration']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $start_date = date('Y-m-d');

    // Calculate end_date
    $end_date = match($duration) {
        '1 Day'    => date('Y-m-d', strtotime("+1 day")),
        '1 Week'   => date('Y-m-d', strtotime("+1 week")),
        '2 Weeks'  => date('Y-m-d', strtotime("+2 weeks")),
        '1 Month'  => date('Y-m-d', strtotime("+1 month")),
        '3 Months' => date('Y-m-d', strtotime("+3 months")),
        '6 Months' => date('Y-m-d', strtotime("+6 months")),
        '1 Year'   => date('Y-m-d', strtotime("+1 year")),
        default    => date('Y-m-d')
    };

    $stmt = $conn->prepare("INSERT INTO memberships (user_id, plan_name, duration, price, description, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsss", $user_id, $services, $duration, $price, $description, $start_date, $end_date);
    $stmt->execute();

    header("Location: manage_membership.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Membership Service</title>
    <style>
    body {
        background: #0d1b2a; /* dark background */
        color: #ffffff;     /* white text */
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
        background-color: #1b263b;
        color: #ffffff;
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .back-arrow:hover {
        background-color: #243a63; /* hover dark blue */
        color: #3b82f6;            /* accent blue */
    }

    form {
        max-width: 500px;
        background: #1b263b; /* dark form background */
        padding: 20px;
        border-radius: 10px;
        margin: 80px auto 0 auto;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
    }

    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #334155;
        background: #0d1b2a;
        color: #ffffff;
        font-size: 16px;
    }

    input:focus, textarea:focus, select:focus {
        outline: 2px solid #3b82f6; /* blue glow */
    }

    button {
        background: #3b82f6; /* blue button */
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #2563eb; /* darker blue */
    }

    label {
        font-weight: bold;
        margin-top: 10px;
        display: block;
        color: #ffffff;
    }

    h2 {
        text-align: center;
        color: #3b82f6; /* accent title */
    }

    .readonly-field {
        background-color: #243a63;
        color: #94a3b8;
        pointer-events: none;
    }
</style>

</head>
<body>

<!-- Back Arrow -->
<a href="manage_membership.php" class="back-arrow">&#8592;</a>

<h2>Add New Service Membership</h2>
<form method="POST">
    <label>Assign to Customer</label>
    <select name="user_id" id="user_id" required onchange="updateContactDetails()">
        <option value="">-- Select Customer --</option>
        <?php
        $userData = [];
        mysqli_data_seek($users_result, 0);
        while ($user = $users_result->fetch_assoc()):
            $userData[$user['id']] = [
                'email' => $user['email'],
                'phone' => $user['phone']
            ];
        ?>
            <option value="<?= $user['id'] ?>">
                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Email</label>
    <input type="text" id="user_email" class="readonly-field" readonly>

    <label>Phone</label>
    <input type="text" id="user_phone" class="readonly-field" readonly>

    <label>Services</label>
    <select name="services" required>
        <option value="">-- Select Service --</option>
        <option value="Gym Access">Gym Access</option>
        <option value="Personal Trainer">Personal Trainer</option>
    </select>

    <label>Duration</label>
    <select name="duration" required>
        <option value="">-- Select Duration --</option>
        <option value="1 Day">1 Day</option>
        <option value="1 Week">1 Week</option>
        <option value="2 Weeks">2 Weeks</option>
        <option value="1 Month">1 Month</option>
        <option value="3 Months">3 Months</option>
        <option value="6 Months">6 Months</option>
        <option value="1 Year">1 Year</option>
    </select>

    <label>Price (₱)</label>
    <input type="number" step="0.01" name="price" required>

    

    <button type="submit">Add Membership</button>
</form>

<script>
    const userData = <?= json_encode($userData) ?>;

    function updateContactDetails() {
        const userId = document.getElementById('user_id').value;
        const emailField = document.getElementById('user_email');
        const phoneField = document.getElementById('user_phone');

        if (userId && userData[userId]) {
            emailField.value = userData[userId].email;
            phoneField.value = userData[userId].phone;
        } else {
            emailField.value = '';
            phoneField.value = '';
        }
    }
</script>
</body>
</html>
