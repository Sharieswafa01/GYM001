<?php 
session_start();
include('../user/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch new users registered in the last 24 hours
$newUsersQuery = "
    SELECT first_name, last_name, role, created_at 
    FROM users 
    WHERE created_at >= NOW() - INTERVAL 1 DAY 
    ORDER BY created_at DESC
";
$newUsersResult = $conn->query($newUsersQuery);
$newUsers = $newUsersResult ? $newUsersResult->fetch_all(MYSQLI_ASSOC) : [];

// Fetch memberships expiring in the next 7 days
$expiringMembershipsQuery = "
    SELECT u.first_name, u.last_name, m.start_date, m.duration, 
           DATE_ADD(m.start_date, INTERVAL m.duration DAY) AS expiry_date
    FROM memberships m
    JOIN users u ON u.id = m.user_id
    WHERE m.start_date IS NOT NULL AND m.duration IS NOT NULL
      AND DATE_ADD(m.start_date, INTERVAL m.duration DAY) BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY
    ORDER BY expiry_date ASC
";
$expiringResult = $conn->query($expiringMembershipsQuery);
$expiringMemberships = $expiringResult ? $expiringResult->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🔔 Admin Notifications</title>
    <link rel="stylesheet" href="css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e8f5e9);
            margin: 0;
            padding: 50px;
            color: #333;
        }

        .top-nav {
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 8px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background-color: #388e3c;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .notif-box {
            background: #fff;
            border-radius: 12px;
            padding: 25px 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-top: 20px;
        }

        .notif {
            display: flex;
            align-items: flex-start;
            padding: 20px;
            margin-bottom: 16px;
            border-left: 4px solid #4CAF50;
            background: #fdfdfd;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .notif:hover {
            background: #f1f8f4;
        }

        .notif i {
            font-size: 20px;
            margin-right: 15px;
            color: #4CAF50;
            margin-top: 3px;
        }

        .notif-content {
            flex-grow: 1;
        }

        .notif-content strong {
            font-weight: bold;
            color: #222;
        }

        .notif .date {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
            display: block;
        }

        .no-record {
            padding: 15px;
            text-align: center;
            color: #777;
            font-style: italic;
        }

        @media (max-width: 768px) {
            body {
                padding: 30px 15px;
            }
            .notif {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="top-nav">
    <a class="back-btn" href="admin_dashboard.php" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<h1><i class="fas fa-bell"></i> Notifications</h1>

<div class="notif-box">
    <?php if (!empty($newUsers)): ?>
        <?php foreach ($newUsers as $user): ?>
            <div class="notif">
                <i class="fas fa-user-plus"></i>
                <div class="notif-content">
                    <div>
                        <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                        (<?= htmlspecialchars($user['role']) ?>) has registered.
                    </div>
                    <span class="date">🕒 <?= date("F j, Y, g:i a", strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($expiringMemberships)): ?>
        <?php foreach ($expiringMemberships as $mem): ?>
            <div class="notif">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="notif-content">
                    <div>
                        Membership of <strong><?= htmlspecialchars($mem['first_name'] . ' ' . $mem['last_name']) ?></strong> will expire soon.
                    </div>
                    <span class="date">📅 Expiry Date: <?= date("F j, Y", strtotime($mem['expiry_date'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($newUsers) && empty($expiringMemberships)): ?>
        <div class="no-record">
            <i class="fas fa-info-circle"></i> No notifications at the moment.
        </div>
    <?php endif; ?>
</div>

</body>
</html>

