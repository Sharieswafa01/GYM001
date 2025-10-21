<?php
$servername = "localhost"; // Your database host, usually 'localhost'
$username = "root"; // Your database username (default is 'root' on local server)
$password = ""; // Your database password (empty for local by default)
$dbname = "gym_management"; // Name of the new database

// Create PDO connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
