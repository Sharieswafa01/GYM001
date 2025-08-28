<?php 
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['reset_admin_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$adminId = $_SESSION['reset_admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new === $confirm) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
        $update->bind_param("si", $hashed, $adminId);
        $update->execute();

        unset($_SESSION['reset_admin_id']); // Clear session after reset
        $success = "Password reset successfully.";
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      min-height: 100vh;
    }

    .container {
      background: #fff;
      margin: 80px auto;
      padding: 30px 30px 50px;
      border-radius: 16px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: left;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin: 15px 0 8px;
      font-weight: 600;
    }

    .input-group {
      position: relative;
    }

    .input-group input {
      width: 100%;
      padding: 12px 40px 12px 14px;
      font-size: 15px;
      border-radius: 8px;
      border: 2px solid #ccc;
      outline: none;
    }

    .input-group i.toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }

    .btn {
      width: 100%;
      background: #43a047;
      color: white;
      padding: 12px;
      font-size: 16px;
      margin-top: 30px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    .btn:hover {
      background: #388e3c;
    }

    .msg, .error {
      text-align: center;
      margin-top: 10px;
      padding: 10px;
      border-radius: 6px;
    }

    .msg {
      background-color: #c8e6c9;
      color: #2e7d32;
    }

    .error {
      background-color: #ffcdd2;
      color: #c62828;
    }

    .back-button {
      position: fixed;
      top: 20px;
      left: 20px;
    }

    .back-button a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background-color: white;
      color: #4CAF50;
      border-radius: 8px;
      font-size: 18px;
      text-decoration: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<div class="back-button">
  <a href="admin_login.php" title="Back"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="container">
  <h2>Reset Password</h2>

  <?php if (isset($error)): ?>
    <div class="error"><?= $error ?></div>
  <?php elseif (isset($success)): ?>
    <div class="msg"><?= $success ?></div>
  <?php endif; ?>

  <form method="post">
    <label>New Password</label>
    <div class="input-group">
      <input type="password" name="new_password" id="new_password" required>
      <i class="fas fa-eye toggle" toggle="#new_password"></i>
    </div>

    <label>Confirm New Password</label>
    <div class="input-group">
      <input type="password" name="confirm_password" id="confirm_password" required>
      <i class="fas fa-eye toggle" toggle="#confirm_password"></i>
    </div>

    <button type="submit" class="btn">Reset Password</button>
  </form>
</div>

<script>
  document.querySelectorAll('.toggle').forEach(icon => {
    icon.addEventListener('click', function () {
      const input = document.querySelector(this.getAttribute('toggle'));
      if (input.type === "password") {
        input.type = "text";
        this.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        this.classList.replace("fa-eye-slash", "fa-eye");
      }
    });
  });
</script>

</body>
</html>
