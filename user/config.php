<?php
$host = "localhost"; 
$dbname = "gym_management"; 
$username = "root"; 
$password = ""; // default in XAMPP is empty

// Create PDO connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>
