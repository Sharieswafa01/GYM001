<?php
session_start();
include 'db_connection.php';
date_default_timezone_set('Asia/Manila');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = trim($_POST['user_id']);

    if (empty($user_id)) {
        $_SESSION['message'] = "Please enter an ID number.";
        $_SESSION['message_type'] = "error";
        header("Location: user_login.php");
        exit();
    }

    if (!isset($_SESSION['logged_users'])) {
        $_SESSION['logged_users'] = [];
    }

    // Check if this user is already logged in for the session
    $already_logged_in = false;
    foreach ($_SESSION['logged_users'] as $logged_user_entry) {
        if ($logged_user_entry['user_id'] === $user_id) {
            $already_logged_in = true;
            break;
        }
    }

    if ($already_logged_in) {
        $_SESSION['message'] = "Already logged in.";
        $_SESSION['message_type'] = "error";
        header("Location: user_login.php");
        exit();
    }

    // Look for user in the database
    $query = "SELECT * FROM users WHERE student_id = ? OR customer_id = ? OR faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Store session details
        $userInfo = [
            'user_id' => $user_id,
            'internal_id' => $user['id'],
            'name' => $user['first_name'] . " " . $user['last_name'],
            'age' => $user['age'],
            'gender' => $user['gender'],
            'login_time' => date("Y-m-d h:i:s A"),
            'type' => 'Unknown',
            'course' => '',
            'section' => '',
        ];

        if ($user['student_id'] === $user_id) {
            $userInfo['type'] = 'Student';
            $userInfo['course'] = $user['course'];
            $userInfo['section'] = $user['section'];
        } elseif ($user['customer_id'] === $user_id) {
            $userInfo['type'] = 'Customer';
        } elseif ($user['faculty_id'] === $user_id) {
            $userInfo['type'] = 'Faculty';
        }

        $_SESSION['current_user_info'] = $userInfo;

        // Insert into attendance table
        $loginTime = date("Y-m-d H:i:s");
        $insert = $conn->prepare("
            INSERT INTO attendance (user_id, status, login_time) 
            VALUES (?, 'Login', ?)
        ");
        $insert->bind_param("is", $user['id'], $loginTime);
        $insert->execute();
        $insert->close();

        // Add to logged_users session
        if (!$already_logged_in) {
            $_SESSION['logged_users'][] = $userInfo;
        }

        $_SESSION['notification'] = "Login Successful";
        $_SESSION['notification_type'] = "login-success";

    } else {
        $_SESSION['message'] = "Invalid ID. Please try again.";
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
    $conn->close();
    header("Location: user_login.php");
    exit();
}
?>
