<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include('../../user/db_connection.php'); // assumes this creates $conn (mysqli)

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Ensure it's GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit;
}

// Optional: filter by id -> /announcements.php?id=3
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();
    echo json_encode($announcement ?: []);
    $stmt->close();
} else {
    // Fetch all announcements
    $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $announcements = [];

    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }

    echo json_encode($announcements);
}

$conn->close();
