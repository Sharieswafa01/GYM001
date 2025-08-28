<?php
session_start();

// Security headers to prevent caching and allow no "back after logout"
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Create CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid request. Please reload the page.");
    }

    // Sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'gym_management');
    if ($conn->connect_error) {
        die("Database connection failed. Please try again later.");
    }

    // Prepare statement
    $stmt = $conn->prepare("SELECT admin_id, email, password, role FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = $admin['role'];
            $_SESSION['last_activity'] = time();

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "No admin found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/admin_login.css">
    <style>
        .forgot-password {
            font-size: 0.9em;
            text-decoration: none;
            color: #007BFF;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>

        <?php if (!empty($error_message)): ?>
            <div class="notification error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
            <div class="notification success">Sign-up successful! Please log in.</div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Login">

            <div style="text-align: right; margin-top: 5px;">
                <a href="admin_forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>
