<?php
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$adminId = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $query = $conn->prepare("SELECT password FROM admin WHERE admin_id = ?");
    $query->bind_param("i", $adminId);
    $query->execute();
    $result = $query->get_result();
    $admin = $result->fetch_assoc();

    if (password_verify($current, $admin['password'])) {
        if ($new === $confirm) {
            if (strlen($new) >= 8) {
                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
                $update->bind_param("si", $hashed, $adminId);
                $update->execute();
                $success = "Password updated successfully.";
            } else {
                $error = "New password must be at least 8 characters.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Change Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      min-height: 100vh;
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

    .input-group.success input { border-color: #4CAF50; }
    .input-group.error input { border-color: #F44336; }

    .input-group i.toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }

    .requirements {
      margin-top: 8px;
      font-size: 13px;
    }

    .requirements p {
      margin: 4px 0;
      display: flex;
      align-items: center;
      color: #999;
    }

    .requirements p.valid { color: green; }
    .requirements p.invalid { color: red; }

    .requirements p i {
      margin-right: 6px;
    }

    .btn {
      width: 100%;
      background:  #43a047;
      color: white;
      padding: 12px;
      font-size: 16px;
      margin-top: 30px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    .btn:hover {
      background: #43a047;
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

    .forgot-link {
      margin-top: 10px;
      text-align: right;
    }

    .forgot-link a {
      font-size: 13px;
      color:  #43a047;
      text-decoration: none;
    }

    .forgot-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .container { margin: 60px 20px; }
    }
  </style>
</head>
<body>

<div class="back-button">
  <a href="admin_profile.php" title="Back"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="container">
  <h2>Change Password</h2>

  <?php if (isset($error)): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>

  <?php if (isset($success)): ?>
    <div class="msg"><?= $success ?></div>
  <?php endif; ?>

  <form method="post" id="changePassForm">
    <label>Old Password</label>
    <div class="input-group" id="group-old">
      <input type="password" name="current_password" id="old_password" required />
      <i class="fas fa-eye toggle" toggle="#old_password"></i>
    </div>

    <label>New Password</label>
    <div class="input-group" id="group-new">
      <input type="password" name="new_password" id="new_password" required minlength="8" onkeyup="validatePassword()" />
      <i class="fas fa-eye toggle" toggle="#new_password"></i>
    </div>

    <div class="requirements" id="requirements">
      <p id="minlength" class="invalid"><i class="fas fa-circle"></i> Minimum 8 characters</p>
      <p id="uppercase" class="invalid"><i class="fas fa-circle"></i> One uppercase letter</p>
      <p id="lowercase" class="invalid"><i class="fas fa-circle"></i> One lowercase letter</p>
      <p id="number" class="invalid"><i class="fas fa-circle"></i> One number</p>
      <p id="special" class="invalid"><i class="fas fa-circle"></i> One special character</p>
    </div>

    <label>Confirm New Password</label>
    <div class="input-group">
      <input type="password" name="confirm_password" id="confirm_password" required />
      <i class="fas fa-eye toggle" toggle="#confirm_password"></i>
    </div>

    <button type="submit" class="btn">Change Password</button>

    <div class="forgot-link">
  <a href="forgot_password.php">Forgot Password?</a>
</div>

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

  function validatePassword() {
    const pwd = document.getElementById("new_password").value;
    const reqs = {
      minlength: pwd.length >= 8,
      uppercase: /[A-Z]/.test(pwd),
      lowercase: /[a-z]/.test(pwd),
      number: /\d/.test(pwd),
      special: /[\W_]/.test(pwd)
    };

    Object.entries(reqs).forEach(([key, valid]) => {
      const el = document.getElementById(key);
      el.className = valid ? 'valid' : 'invalid';
    });
  }
</script>

</body>
</html>
