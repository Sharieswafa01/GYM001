<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_name = trim($_POST['equipment_name']);
    $type = trim($_POST['type']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);
    $photo = '';

    $upload_dir = __DIR__ . '/uploads/';
    $db_path = 'uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['equipment_image']['name'])) {
        $original_name = basename($_FILES['equipment_image']['name']);
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extension, $allowed_types)) {
            $unique_name = uniqid('equip_', true) . '.' . $extension;
            $target_file = $upload_dir . $unique_name;
            $photo = $db_path . $unique_name;

            if (!move_uploaded_file($_FILES['equipment_image']['tmp_name'], $target_file)) {
                die("❌ Error uploading file.");
            }
        } else {
            die("❌ Invalid file type. Only JPG, JPEG, PNG, GIF, or WEBP allowed.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO equipment (equipment_name, type, status, description, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $equipment_name, $type, $status, $description, $photo);
    $stmt->execute();

    header("Location: manage_equipment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Equipment</title>
    <style>
:root {
    --sidebar-dark: #1b263b;
    --bg-dark: #0d1b2a;
    --text-light: #e0e0e0;
    --accent-blue: #2196F3;
    --accent-red: #ff5252;
}

body {
    font-family: Arial, sans-serif;
    background-color: var(--bg-dark);
    color: var(--text-light);
    padding: 50px;
    position: relative;
}

/* Back button */
.back-link {
    position: absolute;
    top: 20px;
    left: 30px;
    font-size: 16px;
    text-decoration: none;
    background-color: var(--sidebar-dark);
    color: var(--text-light);
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-link:hover {
    background-color: #2d3748;
    color: var(--accent-blue);
}

/* Form container */
.container {
    max-width: 600px;
    margin: auto;
    background: var(--sidebar-dark);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: var(--accent-blue);
}

/* Inputs */
input[type="text"],
textarea,
select,
input[type="file"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #2e3b55;
    border-radius: 6px;
    background: var(--bg-dark);
    color: var(--text-light);
}

input:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: var(--accent-blue);
}

/* Buttons */
button {
    background-color: var(--accent-blue);
    color: white;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    transition: background 0.3s ease;
}

button:hover {
    background-color: #1976D2; /* darker blue */
}
</style>

</head>
<body>

<a href="manage_equipment.php" class="back-link"> ← </a>

<div class="container">
    <h2>Add New Gym Equipment</h2>
    <form action="add_equipment.php" method="POST" enctype="multipart/form-data">
        <label>Equipment Name:</label>
        <input type="text" name="equipment_name" required>

        <label>Type:</label>
        <input type="text" name="type" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="In Use">In Use</option>
            <option value="Maintenance">Maintenance</option>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="4" placeholder="Add details about the equipment..."></textarea>

        <label>Upload Image:</label>
        <input type="file" name="equipment_image" accept="image/*">

        <button type="submit">Add Equipment</button>
    </form>
</div>

</body>
</html>
