<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

if (!isset($_GET['id'])) {
    header("Location: manage_equipment.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch existing equipment data
$stmt = $conn->prepare("SELECT * FROM equipment WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>Update Equipment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 40px;
            position: relative;
        }

        /* Back arrow style */
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 16px;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-arrow:hover {
            background-color: rgb(1, 255, 153);
            color: #000;
        }

        h2 {
            text-align: center;
            color: black;
            margin-top: 60px;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
        }

        input[type='text'],
        textarea,
        select,
        input[type='file'] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<a href='manage_equipment.php' class='back-arrow'> ← </a>

<div class='container'>
<h2>Update Equipment Info</h2>";

if ($result->num_rows === 0) {
    echo "<p>Equipment not found.</p>";
} else {
    $equipment = $result->fetch_assoc();
    echo "<form action='update_equipment.php?id={$id}' method='POST' enctype='multipart/form-data'>
        <label>Equipment Name:</label>
        <input type='text' name='equipment_name' value='" . htmlspecialchars($equipment['equipment_name']) . "' required>

        <label>Type:</label>
        <input type='text' name='type' value='" . htmlspecialchars($equipment['type']) . "' required>

        <label>Status:</label>
        <select name='status' required>
            <option value='Available'" . ($equipment['status'] === 'Available' ? ' selected' : '') . ">Available</option>
            <option value='In Use'" . ($equipment['status'] === 'In Use' ? ' selected' : '') . ">In Use</option>
            <option value='Maintenance'" . ($equipment['status'] === 'Maintenance' ? ' selected' : '') . ">Maintenance</option>
        </select>

        <label>Description:</label>
        <textarea name='description' rows='4'>" . htmlspecialchars($equipment['description']) . "</textarea>

        <label>Upload New Image (optional):</label>
        <input type='file' name='equipment_image' accept='image/*'>

        <button type='submit'>Update Equipment</button>
    </form>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_name = trim($_POST['equipment_name']);
    $type = trim($_POST['type']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);

    $image_path = $equipment['image_path'];
    if (!empty($_FILES['equipment_image']['name'])) {
        $target_dir = "../uploads/";
        $new_image = basename($_FILES['equipment_image']['name']);
        $target_file = $target_dir . $new_image;
        move_uploaded_file($_FILES["equipment_image"]["tmp_name"], $target_file);
        $image_path = $new_image;
    }

    $update = $conn->prepare("UPDATE equipment SET equipment_name=?, type=?, status=?, description=?, image_path=? WHERE id=?");
    $update->bind_param("sssssi", $equipment_name, $type, $status, $description, $image_path, $id);
    $update->execute();

    echo "<script>window.location.href='manage_equipment.php';</script>";
}

echo "</div></body></html>";
?>
