<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php'; 
require __DIR__ . '/../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    // Sanitize inputs
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $age          = (int)($_POST['age'] ?? 0);
    $gender       = trim($_POST['gender'] ?? '');
    $email        = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone        = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $role         = trim($_POST['role'] ?? '');
    $payment_plan = trim($_POST['payment_plan'] ?? '');
    $services     = trim($_POST['services'] ?? '');

    if (!$first_name || !$last_name || !$email || !$phone || !$role) {
        throw new Exception("Required fields are missing.");
    }

    // Role-specific
    $student_id = $course = $section = $customer_id = $faculty_id = $faculty_dept = null;
    $user_id = null;

    if ($role === "Student") {
        $student_id = trim($_POST['student_id'] ?? '');
        $course     = trim($_POST['course'] ?? '');
        $section    = trim($_POST['section'] ?? '');
        $user_id    = $student_id;
    } elseif ($role === "Customer") {
        $customer_id = str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        $user_id     = $customer_id;
    } elseif ($role === "Faculty") {
        $faculty_id   = trim($_POST['faculty_id'] ?? '');
        $faculty_dept = trim($_POST['faculty_dept'] ?? '');
        $user_id      = $faculty_id;
    } else {
        throw new Exception("Invalid role selected.");
    }

    // Check duplicates
    $check_sql = "SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([':email' => $email, ':phone' => $phone]);
    if ($check_stmt->fetch()) {
        throw new Exception("This email or phone number is already registered.");
    }

    // Barcode save paths (dynamic project folder)
    $barcode_dir_fs  = __DIR__ . '/../barcodes/';
    $project_name    = basename(dirname(__DIR__)); // dynamically detects folder, e.g., "GYM001"
    $barcode_dir_web = '/' . $project_name . '/barcodes/';

    if (!is_dir($barcode_dir_fs)) {
        mkdir($barcode_dir_fs, 0755, true);
    }

    $barcode_filename = $user_id . '.png';
    $barcode_path_fs  = $barcode_dir_fs . $barcode_filename;
    $barcode_path_web = $barcode_dir_web . $barcode_filename;

    // Insert user
    $sql = "INSERT INTO users (
                first_name, last_name, age, gender, email, phone, role,
                student_id, course, section,
                customer_id, payment_plan, services,
                faculty_id, faculty_dept, barcode_path
            ) VALUES (
                :first_name, :last_name, :age, :gender, :email, :phone, :role,
                :student_id, :course, :section,
                :customer_id, :payment_plan, :services,
                :faculty_id, :faculty_dept, :barcode_path
            )";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':first_name'   => $first_name,
        ':last_name'    => $last_name,
        ':age'          => $age,
        ':gender'       => $gender,
        ':email'        => $email,
        ':phone'        => $phone,
        ':role'         => $role,
        ':student_id'   => $student_id,
        ':course'       => $course,
        ':section'      => $section,
        ':customer_id'  => $customer_id,
        ':payment_plan' => $payment_plan,
        ':services'     => $services,
        ':faculty_id'   => $faculty_id,
        ':faculty_dept' => $faculty_dept,
        ':barcode_path' => $barcode_path_web
    ]);

    // Generate and save barcode image
    $generator = new BarcodeGeneratorPNG();
    $barcode   = $generator->getBarcode($user_id, $generator::TYPE_CODE_128);
    file_put_contents($barcode_path_fs, $barcode);

    // ✅ Redirect to barcode display page
    header("Location: /{$project_name}/user/barcode_display.php?id=" . urlencode($user_id));
    exit();

} catch (Exception $e) {
    die("SIGNUP ERROR: " . $e->getMessage());
}
