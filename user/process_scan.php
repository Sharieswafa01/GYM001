<?php
session_start();
include('db_connection.php'); // your DB connect

if (!isset($_POST['scan_id'])) {
    header("Location: user_login.php");
    exit();
}

$userId = trim($_POST['scan_id']);

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['notification'] = "Invalid ID scanned.";
    $_SESSION['notification_type'] = "error";
    header("Location: user_login.php");
    exit();
}

// Check if user is already logged in
if (isset($_SESSION['current_user_info']) && $_SESSION['current_user_info']['user_id'] == $userId) {
    // Perform logout
    $logoutTime = date("Y-m-d H:i:s");
    $_SESSION['last_logged_out_user_info'] = $_SESSION['current_user_info'];
    $_SESSION['last_logged_out_user_info']['logout_time'] = $logoutTime;

    unset($_SESSION['current_user_info']);

    $_SESSION['notification'] = "User {$user['name']} logged out.";
    $_SESSION['notification_type'] = "logout-success";
} else {
    // Perform login
    $loginTime = date("Y-m-d H:i:s");
    $_SESSION['current_user_info'] = $user;
    $_SESSION['current_user_info']['login_time'] = $loginTime;

    $_SESSION['notification'] = "Welcome {$user['name']}! Logged in successfully.";
    $_SESSION['notification_type'] = "login-success";
}

header("Location: user_login.php");
exit();
