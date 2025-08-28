<?php
include('../user/db_connection.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_users.php");
    exit();
}

// Fetch user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $payment_plan = $_POST['payment_plan'];
    $services = $_POST['services'];

    $updateQuery = "UPDATE users SET first_name=?, last_name=?, age=?, gender=?, email=?, phone=?, payment_plan=?, services=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssisssssi", $first_name, $last_name, $age, $gender, $email, $phone, $payment_plan, $services, $id);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            padding: 60px 20px;
        }

        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            position: relative;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            height: 42px;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        input[disabled] {
            background-color: #e9e9e9;
            color: #555;
        }

        button {
            margin-top: 25px;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
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

        p {
            font-size: 16px;
            color: #555;
        }

        .month-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>

<a class="back-arrow" href="manage_users.php"> ← </a>

<div class="form-container">
    <h2>Edit Customer</h2>
    <form method="POST">
        <label>Customer ID</label>
        <input type="text" value="<?= htmlspecialchars($user['customer_id']) ?>" disabled>

        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

        <label>Age</label>
        <input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

        <label>Role</label>
        <input type="text" value="<?= htmlspecialchars($user['role']) ?>" disabled>

        <label>Payment Plan</label>
        <input type="text" name="payment_plan" value="<?= htmlspecialchars($user['payment_plan']) ?>">

        <label>Services</label>
        <textarea name="services"><?= htmlspecialchars($user['services']) ?></textarea>

        <button type="submit">Update User</button>
    </form>
</div>

</body>
</html>
