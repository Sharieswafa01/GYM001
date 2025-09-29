<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// File name for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users_list.csv');

$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['Role', 'ID', 'Name', 'Course/Department/Plan', 'Section/Services', 'Email', 'Phone']);

// Students
$students = $conn->query("SELECT * FROM users WHERE role='Student'");
while ($row = $students->fetch_assoc()) {
    fputcsv($output, [
        'Student',
        $row['student_id'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['course'],
        $row['section'],
        $row['email'],
        $row['phone']
    ]);
}

// Customers
$customers = $conn->query("SELECT * FROM users WHERE role='Customer'");
while ($row = $customers->fetch_assoc()) {
    fputcsv($output, [
        'Customer',
        $row['customer_id'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['payment_plan'],
        $row['services'],
        $row['email'],
        $row['phone']
    ]);
}

// Staff (Faculty internally)
$staff = $conn->query("SELECT * FROM users WHERE role='Faculty'");
while ($row = $staff->fetch_assoc()) {
    fputcsv($output, [
        'Staff',
        $row['faculty_id'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['faculty_dept'],
        '',
        $row['email'],
        $row['phone']
    ]);
}

fclose($output);
exit();
?>
