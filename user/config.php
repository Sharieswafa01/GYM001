<?php
$servername = "localhost"; // Your database host, usually 'localhost'
$username = "root"; // Your database username (default is 'root' on local server)
$password = ""; // Your database password (empty for local by default)
$dbname = "gym_management"; // Name of the new database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
