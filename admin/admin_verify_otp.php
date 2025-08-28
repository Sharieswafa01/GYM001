<?php
session_start();
include("../user/db_connection.php");

$message = "";

// Ensure the reset_email is set from forgot password step
if (!isset($_SESSION['reset_email'])) {
    header("Location: admin_forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp_entered = trim($_POST['otp']);

    if (!empty($otp_entered)) {
        // Check OTP for this email
        $stmt = $conn->prepare("SELECT otp, otp_expiry FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $otp_db = $row['otp'];
            $expiry_db = $row['otp_expiry'];

            if ($otp_entered == $otp_db) {
                if (strtotime($expiry_db) >= time()) {
                    // OTP valid → allow password reset
                    $_SESSION['otp_verified'] = true;
                    header("Location: admin_reset_password.php");
                    exit();
                } else {
                    $message = "❌ OTP expired. Please request a new one.";
                }
            } else {
                $message = "❌ Invalid OTP.";
            }
        } else {
            $message = "❌ No account found.";
        }
    } else {
        $message = "⚠ Please enter your OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #3a6186, #89253e);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            text-align: center;
            letter-spacing: 3px;
        }
        button {
            background: #3a6186;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #2b4a67;
        }
        .message {
            margin-top: 15px;
            font-size: 13px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <form method="POST" action="">
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required>
            <button type="submit">Verify OTP</button>
            <?php if (!empty($message)) { echo "<div class='message'>$message</div>"; } ?>
        </form>
    </div>
</body>
</html>
        