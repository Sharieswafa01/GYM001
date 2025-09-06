<?php
session_start();

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Determine if a user is currently logged in
$loggedIn = isset($_SESSION['current_user_info']);
$currentUser = $loggedIn ? $_SESSION['current_user_info'] : null;

// Determine if a user just logged out (to display their info post-logout)
$loggedOutRecently = isset($_SESSION['last_logged_out_user_info']);
$lastLoggedOutUser = $loggedOutRecently ? $_SESSION['last_logged_out_user_info'] : null;


// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "gym_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$announcements = [];
$query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Login - CTU Danao Gym</title>
    <link rel="stylesheet" href="css/user_login.css">
    <style>
        /* Your existing CSS styles go here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 40px 0;
        }

        .notification {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
            text-align: center;
        }

        .login-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .logout-success {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .left-section,
        .right-section {
            width: 50%;
            padding: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .logo-text {
            font-weight: bold;
            font-size: 20px;
        }

        form label {
            display: block;
            margin-top: 10px;
        }

        form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .form-buttons button {
            flex: 1;
            padding: 10px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn {
            background-color: #28a745;
            color: white;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
        }

        .signup-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .signup-link:hover {
            text-decoration: underline;
        }

        .right-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-info p {
            margin: 5px 0;
        }

        /* Announcement box - removed width/max-width since container handles width */
        .announcement-box {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            margin-top: 20px;
        }

        .announcement-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .announcement-item {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .announcement-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .announcement-title {
            font-weight: bold;
            color: #007bff;
        }

        .announcement-date {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .announcement-message {
            white-space: pre-wrap;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="wrapper">


        <div class="container">
            <div class="left-section">
                <div class="logo-container">
                    <img src="images/ctu_logo.png" alt="CTU Logo" style="max-width: 40px; max-height: 40px; border: 1px solid #ccc;">
                    <span class="logo-text">CTU GYMTECH</span>
                </div>

                <form method="POST">
                    <label for="user_id">ID NO:</label>
                    <input type="text" name="user_id" id="user_id" required autocomplete="off">

                    <div class="form-buttons">
                        <button type="submit" formaction="process_login.php" class="login-btn">Login</button>
                        <button type="submit" formaction="process_logout.php" class="logout-btn">Logout</button>
                    </div>
                </form>

                <a href="signup.php" class="signup-link">Register</a>
            </div>

            <div class="right-section">
                <h2>User Information</h2>

                <?php if (isset($_SESSION['notification'])): ?>
                    <div class="notification <?= htmlspecialchars($_SESSION['notification_type']) ?>">
                        <?= htmlspecialchars($_SESSION['notification']) ?>
                    </div>
                    <?php unset($_SESSION['notification'], $_SESSION['notification_type']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="notification <?= htmlspecialchars($_SESSION['message_type'] ?? 'error') ?>">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <?php
                // Logic to display user information: prioritize current login, then last logout
                $displayUser = null;
                $displayLoginTime = null;
                $displayLogoutTime = null;

                if ($loggedIn && $currentUser) {
                    // If currently logged in, display current user info
                    $displayUser = $currentUser;
                    $displayLoginTime = $currentUser['login_time'];
                    // Reset last_logged_out_user_info if a new user logs in
                    unset($_SESSION['last_logged_out_user_info']);
                } elseif ($loggedOutRecently && $lastLoggedOutUser) {
                    // If no one is currently logged in, but someone just logged out, display their info
                    $displayUser = $lastLoggedOutUser;
                    // Assuming login_time might still be part of lastLoggedOutUser if captured from logged_users
                    $displayLoginTime = $lastLoggedOutUser['login_time'] ?? 'N/A';
                    $displayLogoutTime = $lastLoggedOutUser['logout_time'];
                }

                if ($displayUser):
                ?>
                    <div class="user-info">
                        <p><strong>Name:</strong> <?= htmlspecialchars($displayUser['name'] ?? 'N/A') ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($displayUser['age'] ?? 'N/A') ?></p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($displayUser['gender'] ?? 'N/A') ?></p>
                        <p><strong>Role:</strong> <?= htmlspecialchars($displayUser['type'] ?? 'N/A') ?></p>

                        <?php if (isset($displayUser['type']) && $displayUser['type'] == 'Student'): ?>
                            <p><strong>Course:</strong> <?= htmlspecialchars($displayUser['course'] ?? 'N/A') ?></p>
                            <p><strong>Section:</strong> <?= htmlspecialchars($displayUser['section'] ?? 'N/A') ?></p>
                        <?php endif; ?>

                        <?php if ($displayLoginTime): ?>
                            <p><strong>Login Time:</strong> <?= htmlspecialchars($displayLoginTime) ?></p>
                        <?php endif; ?>

                        <?php if ($displayLogoutTime): ?>
                            <p><strong>Logout Time:</strong> <?= htmlspecialchars($displayLogoutTime) ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p>Please login to view your information.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container">
            <div class="announcement-box">
                <h3>Latest Announcements</h3>
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item">
                            <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                            <div class="announcement-date"><?= date("M j, Y g:i A", strtotime($announcement['created_at'])) ?></div>
                            <div class="announcement-message"><?= nl2br(htmlspecialchars($announcement['message'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No announcements posted yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>