<?php
require_once 'db_connection.php'; // Ensure the DB connection is properly established

$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

// Allow only specific fields to prevent SQL injection
$allowed_fields = ['email', 'phone', 'student_id'];

if (!in_array($field, $allowed_fields) || empty($value)) {
    echo 'invalid';
    exit;
}

$query = "SELECT COUNT(*) FROM users WHERE $field = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo 'error';
    exit;
}

$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo ($count > 0) ? 'exists' : 'available';
?>
