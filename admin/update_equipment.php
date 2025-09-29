<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: manage_equipment.php');
    exit();
}

/* ---------- Handle POST (update) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_name = trim($_POST['equipment_name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Get existing image (so we can keep it if user didn't upload a new one)
    $stmt = $conn->prepare("SELECT image_path FROM equipment WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $image_path = $row ? $row['image_path'] : '';

    // Handle upload if provided
    if (!empty($_FILES['equipment_image']['name'])) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $new_image = basename($_FILES['equipment_image']['name']);
        $target_file = $upload_dir . $new_image;
        if (move_uploaded_file($_FILES['equipment_image']['tmp_name'], $target_file)) {
            $image_path = $new_image;
        }
    }

    $update = $conn->prepare("UPDATE equipment SET equipment_name=?, type=?, status=?, description=?, image_path=? WHERE id=?");
    $update->bind_param("sssssi", $equipment_name, $type, $status, $description, $image_path, $id);
    $update->execute();

    header('Location: manage_equipment.php');
    exit();
}

/* ---------- GET: fetch equipment ---------- */
$stmt = $conn->prepare("SELECT * FROM equipment WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: manage_equipment.php');
    exit();
}

$equipment = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Update Equipment</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        /* Dark theme / blue-cyan palette (no orange) */
        body {
            font-family: Arial, sans-serif;
            background: #0d1b2a;
            color: #e0e6ed;
            margin: 0;
            padding: 40px;
        }

        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 16px;
            text-decoration: none;
            background-color: rgba(255,255,255,0.08);
            color: #e0e6ed;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,0,0,0.45);
            transition: all .25s ease;
            z-index: 1000;
        }
        .back-arrow:hover {
            background-color: #1b263b;
            color: #00b4d8;
        }

        .container {
            max-width: 600px;
            margin: 80px auto 40px auto;
            background: #1b263b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.6);
        }

        h2 {
            text-align: center;
            color: #90e0ef;
            margin: 0 0 18px 0;
            font-size: 1.9rem;
        }

        label {
            display: block;
            margin-top: 12px;
            color: #cfeff8;
            font-weight: 600;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #2c2f38;
            background: #14213d;
            color: #e0e6ed;
            box-sizing: border-box;
            font-size: 1rem;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #00b4d8;
            box-shadow: 0 0 6px rgba(0,180,216,0.12);
        }

        .small-note {
            font-size: 0.9rem;
            color: #9fbccf;
            margin-top: 8px;
        }

        button[type="submit"] {
            margin-top: 18px;
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 6px;
            background: #0077b6;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s ease;
        }
        button[type="submit"]:hover {
            background: #00b4d8;
        }

        /* Responsive tweaks */
        @media (max-width: 600px) {
            .container { margin: 100px 16px 24px 16px; padding: 20px; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <a href="manage_equipment.php" class="back-arrow">&#8592;</a>

    <div class="container">
        <h2>Update Equipment Info</h2>

        <form action="?id=<?= htmlspecialchars($id) ?>" method="post" enctype="multipart/form-data">
            <label for="equipment_name">Equipment Name:</label>
            <input id="equipment_name" type="text" name="equipment_name" value="<?= htmlspecialchars($equipment['equipment_name']) ?>" required>

            <label for="type">Type:</label>
            <input id="type" type="text" name="type" value="<?= htmlspecialchars($equipment['type']) ?>" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Available" <?= ($equipment['status'] === 'Available') ? 'selected' : '' ?>>Available</option>
                <option value="In Use" <?= ($equipment['status'] === 'In Use') ? 'selected' : '' ?>>In Use</option>
                <option value="Maintenance" <?= ($equipment['status'] === 'Maintenance') ? 'selected' : '' ?>>Maintenance</option>
            </select>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($equipment['description']) ?></textarea>

            <label for="equipment_image">Upload New Image (optional):</label>
            <input id="equipment_image" type="file" name="equipment_image" accept="image/*">
            <?php if (!empty($equipment['image_path'])): ?>
                <div class="small-note">Current image: <?= htmlspecialchars($equipment['image_path']) ?></div>
            <?php endif; ?>

            <button type="submit">Update Equipment</button>
        </form>
    </div>
</body>
</html>
