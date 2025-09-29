<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Handle search
$search = $_GET['search'] ?? '';
$search_sql = "";
if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $search_sql = "WHERE equipment_name LIKE '%$safeSearch%' OR type LIKE '%$safeSearch%'";
}

// Fetch equipment
$query = "SELECT * FROM equipment $search_sql ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Equipment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <style>
:root {
    --sidebar-dark: #1b263b;    /* Sidebar */
    --bg-dark: #0d1b2a;        /* Page background */
    --text-light: #e0e0e0;     /* Main text */
    --accent-blue: #2196F3;    /* Blue highlight */
    --accent-red: #ff5252;     /* Red highlight */
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
    color: var(--text-light);
    padding: 20px 20px 40px;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    border-right: 1px solid #334155;
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
    margin-left: 310px;
    padding: 40px 60px;
    flex: 1;
    background: var(--bg-dark);
    overflow-y: auto;
}

h1 {
    text-align: center;
    color: #e0e6ed;
    margin-bottom: 30px;
}

/* Container */
.container {
    background-color: var(--sidebar-dark);
    color: var(--text-light);
    padding: 30px;
    border-radius: 12px;
    max-width: 1000px;
    margin: auto;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
}

/* Buttons */
.btn-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.btn {
    background-color: var(--accent-blue);
    color: white;
    padding: 10px 16px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background 0.3s ease;
}

.btn:hover {
    background-color: #1976D2; /* darker blue hover */
}

/* Search */
.search-form input[type="text"] {
    padding: 8px;
    width: 250px;
    border-radius: 6px;
    border: 1px solid #2e3b55;
    background: var(--bg-dark);
    color: var(--text-light);
}

.search-form input[type="text"]:focus {
    outline: none;
    border-color: var(--accent-blue);
}

.search-form button {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    background-color: var(--accent-blue);
    color: white;
    cursor: pointer;
    margin-left: 5px;
    transition: background 0.3s ease;
}

.search-form button:hover {
    background-color: #1976D2;
}

/* Table */
.equipment-table {
    width: 100%;
    border-collapse: collapse;
}


th, td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #2e3b55;
}

 thead tr {
        background-color: #334155; /* Blue header */
        color: #fff;
    }




tr:nth-child(even) {
    background-color: #14213d;
}



.equipment-image {
    width: 80px;
    height: auto;
    border-radius: 6px;
}

/* Delete */
.actions .btn-delete {
    background-color: var(--accent-red);
    margin-left: 10px;
}

.actions .btn-delete:hover {
    background-color: #c62828;
}

.no-records {
    color: #999;
    padding: 20px 0;
    font-size: 1.1rem;
    text-align: center;
}

/* Responsive */
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
    .btn-container {
        flex-direction: column;
        align-items: flex-start;
    }
    .search-form {
        margin-top: 10px;
        width: 100%;
    }
    .search-form input[type="text"] {
        width: 100%;
        margin-top: 5px;
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
            </ul>
        </nav>
        <div class="sidebar-logout">
            <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <div class="main-content">
        <div class="container">
            <h1>Manage Gym Equipment</h1>

            <div class="btn-container">
                <a href="add_equipment.php" class="btn"><i class="fas fa-plus"></i> Add Equipment</a>
                <form class="search-form" method="GET">
                    <input type="text" name="search" placeholder="Search by Name or Type" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="equipment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="<?= htmlspecialchars($row['photo']) ?>" class="equipment-image" alt="Equipment Image" />
                                    <?php else: ?>
                                        <span style="color: #666;">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td class="actions">
                                    <a href="update_equipment.php?id=<?= $row['id'] ?>" class="btn"><i class="fas fa-edit"></i> Update</a>
                                    <a href="delete_equipment.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this equipment?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-records">No equipment records found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
