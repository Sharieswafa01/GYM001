<?php
session_start();
include('../user/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT admin_id FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['reset_admin_id'] = $admin['admin_id'];
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
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

    input[type="email"] {
      width: 100%;
      padding: 12px 14px;
      font-size: 15px;
      border-radius: 8px;
      border: 2px solid #ccc;
      outline: none;
    }

    .btn {
      width: 100%;
      background: #2979ff;
      color: white;
      padding: 12px;
      font-size: 16px;
      margin-top: 30px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    .btn:hover {
      background: #1565c0;
    }

    .msg, .error {
      text-align: center;
      margin-top: 10px;
      padding: 10px;
      border-radius: 6px;
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
  <h2>Forgot Password</h2>

  <?php if (isset($error)): ?>
    <div class="error"> <?= $error ?> </div>
  <?php endif; ?>

  <form method="post">
    <label for="email">Enter your email</label>
    <input type="email" name="email" id="email" required>
    <button type="submit" class="btn">Submit</button>
  </form>
</div>

</body>
</html>

