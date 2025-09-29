<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('../user/db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $title = trim($_POST['title']);
        $message = trim($_POST['message']);

        if ($title && $message) {
            $stmt = $conn->prepare("INSERT INTO announcements (title, message, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $title, $message);
            $stmt->execute();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

$announcements = [];
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    :root {
        --sidebar-dark: #1b263b;
        --bg-dark: #0d1b2a;
        --text-light: #e0e0e0;
        --accent-blue: #2196F3;
        --accent-red: #ff5252;
    }

    * { box-sizing: border-box; }

    html, body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg-dark);
        color: var(--text-light);
    }

    .dashboard-wrapper {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }

    /* Sidebar */
    .sidebar {
        width: 290px;
        background: var(--sidebar-dark);
        backdrop-filter: blur(6px);
        color: var(--text-light);
        padding: 20px 20px 40px;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        border-right: 1px solid #334155; /* subtle divider */
    }

    .sidebar .logo {
        text-align: center;
        padding: 30px 0 20px;
        border-bottom: 1px solid #334155;
        margin-bottom: 30px;
    }

    .sidebar .logo h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        color: #3b82f6;
    }

    .sidebar .nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar .nav ul li {
        margin: 18px 0;
    }

    .sidebar .nav ul li a {
        font-size: 18px;
        font-weight: 500;
        color: var(--text-light);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar .nav ul li a:hover {
        background-color: #2d3748;
        color: var(--accent-blue);
    }

    .sidebar .nav ul li a.active {
        background-color: #2d3748;
        color: var(--accent-blue);
        font-weight: 600;
    }

    .sidebar-logout a {
        color: var(--text-light);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 60px;
        font-weight: bold;
        font-size: 18px;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar-logout a:hover {
        background-color: #2d3748;
        color: var(--accent-red);
    }

    /* Main content */
    .main-content {
        margin-left: 310px;   /* space from sidebar */
        padding: 50px;
        flex: 1;
        overflow-y: auto;
        background: var(--bg-dark);
    }

    h1 {
        font-size: 2.2rem;
        margin-bottom: 20px;
        color: var(--text-light);
    }

    /* Announcement sections */
    .announcement-section,
    .announcement-list {
        max-width: 800px;
        margin: 0 auto 40px;
    }

    .announcement-section {
        background: #1b263b;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }

    .announcement-section h2,
    .announcement-list h2 {
        font-size: 1.8rem;
        margin-bottom: 25px;
        color: var(--text-light);
        border-bottom: 2px solid var(--accent-blue);
        padding-bottom: 10px;
    }

    .announcement-form input,
    .announcement-form textarea {
        width: 100%;
        padding: 14px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #2e3b55;
        font-size: 1.1rem;
        resize: vertical;
        background: var(--bg-dark);
        color: var(--text-light);
    }

    .announcement-form input:focus,
    .announcement-form textarea:focus {
        outline: none;
        border-color: var(--accent-blue);
    }

    .announcement-form button {
        width: 100%;
        padding: 14px;
        font-size: 1.2rem;
        border: none;
        background: var(--accent-blue);
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .announcement-form button:hover:not(:disabled) {
        background: #1976D2;
    }

    .announcement-card {
        background-color: #1b263b;
        color: #f5f5f5;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
    }

    .announcement-card h3 {
        margin-top: 0;
        margin-bottom: 10px;
    }

    .announcement-card small {
        display: block;
        color: #90a4ae;
        margin-bottom: 15px;
    }

    .announcement-card form button {
        background-color: var(--accent-red);
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .announcement-card form button:hover {
        background-color: #c62828;
    }

    @media (max-width: 768px) {
        .dashboard-wrapper {
            flex-direction: column;
        }
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }
        .main-content {
            margin-left: 0;
            padding: 20px;
        }
        .announcement-section,
        .announcement-list {
            margin: 20px;
        }
    }
</style>

</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="logo">
            <h2>GYM ADMIN</h2>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="attendance_tracking.php"><i class="fas fa-calendar-check"></i> Attendance Tracking</a></li>
                <li><a href="manage_users.php"><i class="fas fa-user-friends"></i> Manage Users</a></li>
                <li><a href="manage_equipment.php"><i class="fas fa-dumbbell"></i> Equipment</a></li>
                <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="manage_membership.php"><i class="fas fa-credit-card"></i> Membership</a></li>
                <li><a href="waiver.php"><i class="fas fa-file-signature" aria-hidden="true"></i> Waiver</a></li>
            </ul>
        </nav>
        <div class="sidebar-logout">
            <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>
    <main class="main-content">
       
        <section class="announcement-section">
            <h2><i class="fas fa-plus-circle"></i> Create New Announcement</h2>
            <form class="announcement-form" method="POST" autocomplete="off">
                <input type="text" name="title" placeholder="Announcement Title" required />
                <textarea name="message" rows="5" placeholder="Announcement Message" required></textarea>
                <button type="submit"><i class="fas fa-paper-plane"></i> Post Announcement</button>
            </form>
        </section>
        <section class="announcement-list">
            <h2><i class="fas fa-bullhorn"></i> Posted Announcements</h2>
            <?php if (!empty($announcements)): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-card">
                        <h3><i class="fas fa-info-circle"></i> <?= htmlspecialchars($announcement['title']) ?></h3>
                        <small><i class="far fa-clock"></i> Posted on: <?= date("F j, Y - g:i A", strtotime($announcement['created_at'])) ?></small>
                        <p><?= nl2br(htmlspecialchars($announcement['message'])) ?></p>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                            <input type="hidden" name="delete_id" value="<?= $announcement['id'] ?>">
                            <button type="submit"><i class="fas fa-trash-alt"></i> Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#666;"><i class="fas fa-exclamation-circle"></i> No announcements posted yet.</p>
            <?php endif; ?>
        </section>
    </main>
</div>
<script>
    const form = document.querySelector('.announcement-form');
    form.addEventListener('submit', () => {
        form.querySelector('button[type="submit"]').disabled = true;
    });
</script>
</body>
</html>
