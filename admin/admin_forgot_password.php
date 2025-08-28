<?php 
session_start();
include("../user/db_connection.php"); // DB connection

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if email exists in admin table
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate OTP & expiry
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", time() + 300); // 5 minutes

            // Save OTP in DB
            $update = $conn->prepare("UPDATE admin SET otp = ?, otp_expiry = ? WHERE email = ?");
            $update->bind_param("iss", $otp, $expiry, $email);
            $update->execute();

            // Store email in session for OTP verification
            $_SESSION['reset_email'] = $email;

            // Email details
            $subject = "Admin Password Reset OTP";
            $body = "Your OTP code is: $otp\n\nThis code will expire in 5 minutes.\n\nIf you did not request this, please ignore.";
            $headers = "From: noreply@yourdomain.com\r\n";
            $headers .= "Reply-To: noreply@yourdomain.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            // Send OTP via email
            if (mail($email, $subject, $body, $headers)) {
                header("Location: admin_verify_otp.php");
                exit();
            } else {
                $message = "❌ Failed to send OTP. Please check your email settings.";
            }

        } else {
            $message = "❌ No admin account found with that email.";
        }
    } else {
        $message = "⚠ Please enter your email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Forgot Password</title>
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
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
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
        <h2>Forgot Password</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your admin email" required>
            <button type="submit">Enter Email</button>
            <?php if (!empty($message)) { echo "<div class='message'>$message</div>"; } ?>
        </form>
    </div>
</body>
</html>
