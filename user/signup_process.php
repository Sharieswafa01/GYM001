<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'config.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $first_name = htmlspecialchars($_POST['first_name'] ?? '');
    $last_name = htmlspecialchars($_POST['last_name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = htmlspecialchars($_POST['gender'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $role = htmlspecialchars($_POST['role'] ?? '');
    $payment_plan = htmlspecialchars($_POST['payment_plan'] ?? '');
    $services = htmlspecialchars($_POST['services'] ?? '');

    // Optional role-specific fields
    $student_id = null;
    $course = null;
    $section = null;
    $customer_id = null;
    $faculty_id = null;
    $faculty_dept = null;

    if ($role == "Student") {
        $student_id = htmlspecialchars($_POST['student_id'] ?? '');
        $course = htmlspecialchars($_POST['course'] ?? '');
        $section = htmlspecialchars($_POST['section'] ?? '');
    } elseif ($role == "Customer") {
        $customer_id = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    } elseif ($role == "Faculty") {
        $faculty_id = htmlspecialchars($_POST['faculty_id'] ?? '');
        $faculty_dept = htmlspecialchars($_POST['faculty_dept'] ?? '');
    }

    // Check for duplicate email or phone
    $check_sql = "SELECT id FROM users WHERE email = ? OR phone = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $email, $phone);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "This email or phone number is already registered.";
        header("Location: signup.php");
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO users (
                first_name, last_name, age, gender, email, phone, role,
                student_id, course, section,
                customer_id, payment_plan, services,
                faculty_id, faculty_dept
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssissssssssssss", 
        $first_name, $last_name, $age, $gender, $email, $phone, $role,
        $student_id, $course, $section,
        $customer_id, $payment_plan, $services,
        $faculty_id, $faculty_dept
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! Please log in.";
        header("Location: user_login.php");
        exit();
    } else {
        error_log("Execute failed: " . $stmt->error);
        $_SESSION['error'] = "An error occurred. Please try again later.";
        header("Location: signup.php");
        exit();
    }
} else {
    header("Location: signup.php");
    exit();

} catch (Exception $e) {
    die("SIGNUP ERROR: " . $e->getMessage());
}
