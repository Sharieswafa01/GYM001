<?php
$id = $_GET['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f9f9f9;
        }
        .qr-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .qr-container img {
            margin-top: 20px;
        }
        .success {
            color: green;
            font-weight: bold;
            font-size: 18px;
        }
        a {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h2 class="success">🎉 Registration Successful!</h2>
        <p>Your ID: <strong><?= htmlspecialchars($id) ?></strong></p>

        <?php if ($id): ?>
            <p>Here is your QR Code:</p>
            <img src="user/generate_qr.php?id=<?= urlencode($id) ?>" alt="QR Code">
        <?php else: ?>
            <p style="color:red;">No ID provided for QR generation.</p>
        <?php endif; ?>

        <a href="user_login.php">➡ Go to Login</a>
    </div>
</body>
</html>
