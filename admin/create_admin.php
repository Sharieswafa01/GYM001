<?php
// Admin registration for one-time setup
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT); // Hash the password

// Database connection
$conn = new mysqli('127.0.0.1', 'root', '', 'gym_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert the admin account
$sql = "INSERT INTO admin (email, password) VALUES ('admin@example.com', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
