<?php
session_start();
include('../user/db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get user ID
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    die("Invalid request.");
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $role       = $user['role']; // keep role fixed

    // Role-based extra fields
    $student_id   = $_POST['student_id']   ?? null;
    $course       = $_POST['course']       ?? null;
    $section      = $_POST['section']      ?? null;
    $customer_id  = $_POST['customer_id']  ?? null;
    $payment_plan = $_POST['payment_plan'] ?? null;
    $services     = $_POST['services']     ?? null;
    $faculty_id   = $_POST['faculty_id']   ?? null;
    $faculty_dept = $_POST['faculty_dept'] ?? null;

    $updateSql = "";
    $params = [];
    $types = "";

    if ($role === "Student") {
        $updateSql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, student_id=?, course=?, section=? WHERE id=?";
        $params = [$first_name, $last_name, $email, $phone, $student_id, $course, $section, $user_id];
        $types  = "sssssssi";
    } elseif ($role === "Customer") {
        $updateSql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, customer_id=?, payment_plan=?, services=? WHERE id=?";
        $params = [$first_name, $last_name, $email, $phone, $customer_id, $payment_plan, $services, $user_id];
        $types  = "sssssssi";
    } elseif ($role === "Faculty") { // internally still "Faculty"
        $updateSql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, faculty_id=?, faculty_dept=? WHERE id=?";
        $params = [$first_name, $last_name, $email, $phone, $faculty_id, $faculty_dept, $user_id];
        $types  = "ssssssi";
    }

    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #0d1b2a; /* Dark navy */
        margin: 0;
        padding: 0;
        color: #e0e6ed; /* Light text */
    }

    .container {
        width: 600px;
        margin: 40px auto;
        background: #1b263b; /* Dark panel */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.6);
        position: relative;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #90e0ef; /* Cyan heading */
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        color: #e0e6ed;
    }

    input, select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #2c2f38;
        border-radius: 6px;
        background: #14213d; /* Dark input background */
        color: #e0e6ed;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #00b4d8; /* Cyan border on focus */
        box-shadow: 0 0 5px #00b4d8;
    }

    button {
        margin-top: 20px;
        padding: 10px;
        width: 100%;
        background: #0077b6; /* Blue button */
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #00b4d8; /* Cyan hover */
    }

    .error {
        color: #ff6b6b; /* Soft red for errors */
        margin-top: 10px;
    }

    /* Back Arrow Style */
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
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .back-arrow:hover {
        background-color: #1b263b;
        color: #00b4d8; /* Cyan accent */
    }
</style>

</head>
<body>

<!-- Back Arrow -->
<a href="manage_users.php" class="back-arrow">&#8592;</a>

<div class="container">
    <h2>
        <?php 
            // Change "Faculty" to "Staff" only in display
            echo ($user['role'] === "Faculty") ? "Edit Staff" : "Edit " . htmlspecialchars($user['role']); 
        ?>
    </h2>

    <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

        <?php if ($user['role'] === "Student"): ?>
            <label>Student ID</label>
            <input type="text" name="student_id" value="<?= htmlspecialchars($user['student_id']) ?>" required>

            <label>Course</label>
            <input type="text" name="course" value="<?= htmlspecialchars($user['course']) ?>" required>

            <label>Section</label>
            <input type="text" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
        <?php elseif ($user['role'] === "Customer"): ?>
            <label>Customer ID</label>
            <input type="text" name="customer_id" value="<?= htmlspecialchars($user['customer_id']) ?>" required>

            <label>Payment Plan</label>
            <input type="text" name="payment_plan" value="<?= htmlspecialchars($user['payment_plan']) ?>" required>

            <label>Services</label>
            <input type="text" name="services" value="<?= htmlspecialchars($user['services']) ?>" required>
        <?php elseif ($user['role'] === "Faculty"): ?>
            <label>Staff ID</label>
            <input type="text" name="faculty_id" value="<?= htmlspecialchars($user['faculty_id']) ?>" required>

            <label>Department</label>
            <input type="text" name="faculty_dept" value="<?= htmlspecialchars($user['faculty_dept']) ?>" required>
        <?php endif; ?>

        <button type="submit">Update User</button>
    </form>
</div>
</body>
</html>
