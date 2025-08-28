<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

// Set current time as the "last seen" timestamp
$_SESSION['last_seen_notifications'] = time();

echo json_encode(['status' => 'reset']);
