<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch photo path to delete it from folder
    $stmt = $conn->prepare("SELECT photo FROM equipment WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($photoPath);
    $stmt->fetch();
    $stmt->close();

    // Delete the record from database
    $stmt = $conn->prepare("DELETE FROM equipment WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete the image file if exists
        if (!empty($photoPath) && file_exists($photoPath)) {
            unlink($photoPath);
        }
        header("Location: manage_equipment.php?success=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
