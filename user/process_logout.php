<?php
session_start();
include 'db_connection.php';
date_default_timezone_set('Asia/Manila');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

$user_id = trim($_POST['user_id']);

if (empty($user_id)) {
    $_SESSION['message'] = "Please enter an ID number.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

if (!isset($_SESSION['logged_users']) || empty($_SESSION['logged_users'])) {
    $_SESSION['message'] = "No users are currently logged in.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

// Find user in logged_users array
$index = null;
$loggedOutUser = null;
foreach ($_SESSION['logged_users'] as $key => $loggedUser) {
    if ($loggedUser['user_id'] === $user_id) {
        $index = $key;
        $loggedOutUser = $loggedUser;
        break;
    }
}

if ($index === null || $loggedOutUser === null) {
    $_SESSION['message'] = "Invalid ID.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

$internalId = $loggedOutUser['internal_id'];
$logoutTime = date("Y-m-d H:i:s");

// Update latest attendance record with no logout time
$update = $conn->prepare("
    UPDATE attendance 
    SET status = 'Logout', logout_time = ? 
    WHERE user_id = ? AND logout_time IS NULL 
    ORDER BY id DESC LIMIT 1
");
$update->bind_param("si", $logoutTime, $internalId);
$update->execute();
$update->close();

// Store last logged out user's info
$_SESSION['last_logged_out_user_info'] = $loggedOutUser;
$_SESSION['last_logged_out_user_info']['logout_time'] = date("Y-m-d h:i:s A");

// Remove active login session
unset($_SESSION['current_user_info']);
unset($_SESSION['logged_users'][$index]);
$_SESSION['logged_users'] = array_values($_SESSION['logged_users']);

$_SESSION['notification'] = "Logout Successful";
$_SESSION['notification_type'] = "logout-success";

$conn->close();
header("Location: user_login.php");
exit();
?>
