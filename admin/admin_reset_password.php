<?php
session_start();
include 'db.php';

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: admin_forgot_password.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            unset($_SESSION['otp_verified'], $_SESSION['otp'], $_SESSION['reset_email']);
            $message = "<span style='color: lightgreen;'>Password successfully updated!</span>";
        } else {
            $message = "<span style='color: red;'>Error updating password.</span>";
        }
    } else {
        $message = "<span style='color: red;'>Passwords do not match.</span>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #373b44, #4286f4);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(255,255,255,0.2);
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        button {
            width: 95%;
            padding: 10px;
            background: #4caf50;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .message {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>
</body>
</html>
