<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';

// ✅ Get user_id either from URL or Session
$user_id = $_GET['id'] ?? ($_SESSION['user_id'] ?? '');
$role = $_SESSION['role'] ?? '';

if (!$user_id) {
    // Still allow faculty/students to continue
    if (in_array($role, ['Student', 'Faculty'])) {
        header("Location: user_login.php");
        exit;
    }
    die("❌ No user ID found. Please return to the signup page.");
}

// Fetch user details
$sql = "SELECT * FROM users 
        WHERE student_id = :id OR customer_id = :id OR faculty_id = :id 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    if (in_array($role, ['Student', 'Faculty'])) {
        header("Location: user_login.php");
        exit;
    }
    die("❌ User not found in the database.");
}

// Extract data
$full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$role = htmlspecialchars($user['role']);
$barcode_web = $user['barcode_path'] ?? '';
$barcode_fs = $_SERVER['DOCUMENT_ROOT'] . $barcode_web;
$barcode_exists = !empty($barcode_web) && file_exists($barcode_fs);

// ✅ Auto redirect students & faculty after short delay
if (in_array($role, ['Student', 'Faculty'])) {
    echo "<script>
        setTimeout(function() {
            window.location.href = 'user_login.php';
        }, 2000);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Successful</title>
<style>
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: linear-gradient(135deg, #1f1c2c, #928dab);
  color: #fff;
  height: 100vh;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}
.success-container {
  background: rgba(0, 0, 0, 0.75);
  padding: 40px;
  border-radius: 14px;
  text-align: center;
  width: 90%;
  max-width: 480px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
}
.success-container h2 {
  color: #4CAF50;
  margin-bottom: 10px;
}
.success-container p {
  font-size: 16px;
  margin: 6px 0;
}
.btn {
  background: #4CAF50;
  color: white;
  text-decoration: none;
  padding: 12px 26px;
  border-radius: 8px;
  display: inline-block;
  margin-top: 25px;
  font-weight: bold;
  transition: background 0.3s ease;
}
.btn:hover {
  background: #45a049;
}
.barcode {
  margin-top: 25px;
  background: #fff;
  padding: 10px;
  border-radius: 8px;
  display: inline-block;
}
.barcode img {
  width: 250px;
  height: auto;
}
.info {
  margin-top: 15px;
  color: #ddd;
}
</style>
</head>
<body>
<div class="success-container">
  <h2>🎉 Registration Successful!</h2>
  <p><strong>Name:</strong> <?= $full_name ?></p>
  <p><strong>Role:</strong> <?= $role ?></p>

  <?php if ($role === 'Student'): ?>
    <p><strong>Student ID:</strong> <?= htmlspecialchars($user['student_id']) ?></p>
    <p><strong>Course & Section:</strong> <?= htmlspecialchars($user['course'] . ' ' . $user['section']) ?></p>
    <p>Redirecting to login...</p>

  <?php elseif ($role === 'Faculty'): ?>
    <p><strong>Faculty ID:</strong> <?= htmlspecialchars($user['faculty_id']) ?></p>
    <p><strong>Department:</strong> <?= htmlspecialchars($user['faculty_dept']) ?></p>
    <p>Redirecting to login...</p>

  <?php elseif ($role === 'Customer'): ?>
    <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customer_id']) ?></p>
    <?php if ($barcode_exists): ?>
      <div class="barcode">
        <p style="color:#000; font-weight:bold;">Your Barcode:</p>
        <img src="<?= htmlspecialchars($barcode_web) ?>" alt="User Barcode">
      </div>
    <?php endif; ?>
    <a href="user_login.php" class="btn">Go to Login</a>
  <?php endif; ?>
</div>
</body>
</html>
