<?php
session_start();
header('Content-Type: application/json');
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

// Get last seen timestamp
$lastSeen = $_SESSION['last_seen_notifications'] ?? 0;
$lastSeenTime = date('Y-m-d H:i:s', $lastSeen);

// Count new users since last seen
$newUsersQuery = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE created_at > ?");
$newUsersQuery->bind_param("s", $lastSeenTime);
$newUsersQuery->execute();
$newUsersCount = $newUsersQuery->get_result()->fetch_assoc()['count'];

// Count expiring memberships since last seen
$expiringQuery = $conn->prepare("
    SELECT COUNT(*) as count
    FROM memberships m
    JOIN users u ON u.id = m.user_id
    WHERE DATE_ADD(m.start_date, INTERVAL m.duration DAY) BETWEEN NOW() AND NOW() + INTERVAL 1 DAY
    AND DATE_ADD(m.start_date, INTERVAL m.duration DAY) > ?
");
$expiringQuery->bind_param("s", $lastSeenTime);
$expiringQuery->execute();
$expiringCount = $expiringQuery->get_result()->fetch_assoc()['count'];

$total = $newUsersCount + $expiringCount;

echo json_encode(['status' => 'ok', 'count' => $total]);
