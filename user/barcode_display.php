<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';

// Get the user_id from URL
$user_id = $_GET['id'] ?? '';
if (!$user_id) {
    die("❌ No user ID provided in the URL.");
}

// Fetch user info
$sql = "SELECT * FROM users 
        WHERE student_id = :id OR customer_id = :id OR faculty_id = :id 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ User not found in the database.");
}

// Barcode path (from DB)
$barcode_web = $user['barcode_path'] ?? '';
$barcode_fs  = $_SERVER['DOCUMENT_ROOT'] . $barcode_web; // full filesystem path

// Check if the file exists
$barcode_exists = file_exists($barcode_fs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Barcode</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px; }
        .card {
            background: white; padding: 20px; border-radius: 10px;
            max-width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .barcode { margin-top: 20px; }
        button {
            margin-top: 20px; padding: 10px 20px;
            background: #007BFF; color: white; border: none;
            border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #0056b3; }
        .error { color: red; margin-top: 10px; }

        /* Back button */
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 16px;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .back-arrow:hover {
            background-color: #00ff99;
            color: #000;
        }
    </style>
</head>
<body>
    <!-- Back arrow -->
    <a href="user_login.php" class="back-arrow"> ⬅ </a>

    <div class="card">
        <h2>✅ Registration Successful</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>

        <?php if ($user['role'] === 'Student'): ?>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($user['student_id']) ?></p>
            <p><strong>Course & Section:</strong> <?= htmlspecialchars($user['course'] . ' ' . $user['section']) ?></p>
        <?php elseif ($user['role'] === 'Customer'): ?>
            <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customer_id']) ?></p>
        <?php elseif ($user['role'] === 'Faculty'): ?>
            <p><strong>Faculty ID:</strong> <?= htmlspecialchars($user['faculty_id']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($user['faculty_dept']) ?></p>
        <?php endif; ?>

        <div class="barcode">
            <?php if ($barcode_exists): ?>
                <img src="<?= htmlspecialchars($barcode_web) ?>" alt="User Barcode">
            <?php else: ?>
                <p class="error">❌ Barcode image not found at: <br><?= htmlspecialchars($barcode_web) ?></p>
            <?php endif; ?>
        </div>

        <button onclick="window.print()">🖨️ Print Barcode</button>
    </div>
</body>
</html>
