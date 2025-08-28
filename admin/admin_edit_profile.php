<?php
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$adminId = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $title = trim($_POST['title']);

    $filename = $_FILES['profile_picture']['name'] ?? '';
    $tempname = $_FILES['profile_picture']['tmp_name'] ?? '';
    $newFileName = '';

    $uploadPath = '../uploads/admins/';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if ($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newFileName = uniqid('admin_', true) . '.' . $ext;
        move_uploaded_file($tempname, $uploadPath . $newFileName);
    }

    if ($newFileName) {
        $query = $conn->prepare("UPDATE admin SET full_name = ?, username = ?, email = ?, title = ?, profile_picture = ? WHERE admin_id = ?");
        $query->bind_param("sssssi", $fullName, $username, $email, $title, $newFileName, $adminId);
    } else {
        $query = $conn->prepare("UPDATE admin SET full_name = ?, username = ?, email = ?, title = ? WHERE admin_id = ?");
        $query->bind_param("ssssi", $fullName, $username, $email, $title, $adminId);
    }

    if ($query->execute()) {
        header("Location: admin_profile.php");
        exit();
    } else {
        $error = "Failed to update profile.";
    }
}

$query = $conn->prepare("SELECT * FROM admin WHERE admin_id = ?");
$query->bind_param("i", $adminId);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin Profile</title>
    <link rel="stylesheet" href="css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            background: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            padding: 60px 20px;
            min-height: 100vh;
        }

        .form-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #1b5e20;
            font-size: 22px;
        }

        label {
            display: block;
            margin: 15px 0 6px;
            font-weight: 600;
            color: #2e7d32;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fdfdfd;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #388e3c;
        }

        .error {
            color: #d32f2f;
            background: #fdecea;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }

        .back-button a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #4CAF50;
            color: white;
            border-radius: 8px;
            font-size: 18px;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background 0.3s ease;
        }

        .back-button a:hover {
            background-color: #43a047;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
            }

            .back-button {
                top: 10px;
                left: 10px;
            }
        }
    </style>
</head>
<body>

<div class="back-button">
    <a href="admin_profile.php" title="Back"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="form-container">
    <h2>Edit Admin Profile</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>

        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($admin['title'] ?? '') ?>">

        <label>Profile Picture</label>
        <input type="file" name="profile_picture" accept="image/*">

        <input type="submit" value="Save Changes">
    </form>
</div>

</body>
</html>
