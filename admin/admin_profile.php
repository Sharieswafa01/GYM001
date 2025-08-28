<?php
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$adminId = $_SESSION['admin_id'];

// Get admin info
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
    <title>Admin Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            padding: 60px 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 25px 30px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }

        .profile-card {
            display: flex;
            align-items: center;
        }

        .profile-top {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .profile-top img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #4CAF50;
        }

        .profile-details h2 {
            margin: 0;
            font-size: 22px;
            color: #2e7d32;
        }

        .profile-details p {
            margin: 5px 0;
            color: #666;
            font-weight: 500;
        }

        .profile-details span {
            font-size: 14px;
            color: #888;
        }

        .info-card .card-header,
        .action-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            color: #1b5e20;
        }

        .edit-btn {
            font-size: 14px;
            color: #4CAF50;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border: 1px solid #4CAF50;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #4CAF50;
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .info-grid label {
            font-weight: 600;
            font-size: 14px;
            color: #2e7d32;
        }

        .info-grid p {
            margin: 4px 0 0;
            font-size: 15px;
            color: #333;
        }

        .action-buttons a {
            display: inline-block;
            font-size: 14px;
            color: white;
            background-color: #4CAF50;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .action-buttons a:hover {
            background-color: #388e3c;
        }

        /* Back button — DO NOT MODIFY */
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
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="back-button">
    <a href="admin_dashboard.php" title="Back"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="container">
    <?php if ($admin): ?>
        <div class="card profile-card">
            <div class="profile-top">
                <img src="<?= !empty($admin['profile_picture']) ? '../uploads/admins/' . htmlspecialchars($admin['profile_picture']) : 'default_user.png' ?>" alt="Admin Photo">
                <div class="profile-details">
                    <h2><?= htmlspecialchars($admin['full_name'] ?? 'N/A') ?></h2>
                    <p><?= htmlspecialchars($admin['title'] ?? 'N/A') ?></p>
                    <span><?= htmlspecialchars($admin['email'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>

        <div class="card info-card">
            <div class="card-header">
                <h3>Personal Information</h3>
                <a href="admin_edit_profile.php" class="edit-btn"><i class="fas fa-pen"></i> Edit</a>
            </div>
            <div class="info-grid">
                <div>
                    <label>Full Name</label>
                    <p><?= htmlspecialchars($admin['full_name'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label>Username</label>
                    <p><?= htmlspecialchars($admin['username'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label>Email</label>
                    <p><?= htmlspecialchars($admin['email'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label>Title</label>
                    <p><?= htmlspecialchars($admin['title'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <div class="card action-card">
            <div class="card-header">
                <h3>Security</h3>
            </div>
            <div class="action-buttons">
                <a href="admin_change_password.php"><i class="fas fa-key"></i> Change Password</a>
            </div>
        </div>
    <?php else: ?>
        <p style="color: red; text-align:center;">Admin profile not found.</p>
    <?php endif; ?>
</div>

</body>
</html>


