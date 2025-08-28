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
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        .dashboard-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 290px;
            background-color: #111;
            color: #fff;
            height: 100vh;
            padding: 20px;
            position: fixed;
            overflow-y: auto;
        }

        .sidebar .logo {
            text-align: center;
            padding: 30px 0 20px;
            border-bottom: 1px solid #444;
            margin-bottom: 30px;
        }

        .sidebar .logo h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .sidebar .nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar .nav ul li {
            margin: 18px 0;
        }

        .sidebar .nav ul li a {
            font-size: 18px;
            font-weight: 500;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar .nav ul li a:hover {
            background-color: #333;
            color: #4caf50;
        }

        .sidebar-logout a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 60px;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar-logout a:hover {
            background-color: #333;
            color: #f44336;
        }

        .main-content {
            margin-left: 290px;
            padding: 40px 60px;
            flex: 1;
            background-color: #fff;
            overflow-y: auto;
        }

        h1 {
            text-align: center;
            color: #000;
            margin-bottom: 30px;
        }

        .container {
            background-color: #ffffff;
            color: #000000;
            padding: 30px;
            border-radius: 12px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #218838;
        }

        .search-form input[type="text"] {
            padding: 8px;
            width: 250px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            margin-left: 5px;
        }

        .equipment-table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .equipment-image {
            width: 80px;
            height: auto;
            border-radius: 6px;
        }

        .actions .btn-delete {
            background-color: #f44336;
            margin-left: 10px;
        }

        .actions .btn-delete:hover {
            background-color: #d32f2f;
        }

        .no-records {
            color: #555;
            padding: 20px 0;
            font-size: 1.1rem;
            text-align: center;
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
            <h2>Gym Admin</h2>
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
