<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Handle search
$search = $_GET['search'] ?? '';
$searchSql = '';
if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $searchSql = "AND (first_name LIKE '%$safeSearch%' OR last_name LIKE '%$safeSearch%' OR role LIKE '%$safeSearch%')";
}

$limit = 5;

$studentsQuery = "SELECT * FROM users WHERE role = 'Student' $searchSql LIMIT $limit";
$customersQuery = "SELECT * FROM users WHERE role = 'Customer' $searchSql LIMIT $limit";
$facultyQuery = "SELECT * FROM users WHERE role = 'Faculty' $searchSql LIMIT $limit";

$studentsResult = mysqli_query($conn, $studentsQuery);
$customersResult = mysqli_query($conn, $customersQuery);
$facultyResult = mysqli_query($conn, $facultyQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        .dashboard-wrapper { display: flex; }

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
            color: #fff;
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
            padding: 40px;
            flex: 1;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0;
            color: black;
            font-size: 2rem;
        }

        .search-form input[type="text"] {
            padding: 8px;
            width: 240px;
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
        }

        section {
            margin-bottom: 40px;
        }

        .user-section {
            margin-bottom: 60px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .view-all-link {
            display: block;
            margin-top: 10px;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }

        thead tr {
            background-color: rgb(242, 241, 241);
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            padding: 6px 0;
        }

        a.update-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.75rem;
            text-decoration: none;
        }

        a.update-btn:hover {
            background-color: #45a049;
        }

        a.delete-btn {
            background-color: #f44336;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.75rem;
            text-decoration: none;
        }

        a.delete-btn:hover {
            background-color: #d32f2f;
        }

        @media (max-width: 768px) {
            .dashboard-wrapper { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; padding: 20px; }
            .header-bar { flex-direction: column; align-items: flex-start; gap: 15px; }
            .search-form input[type="text"] { width: 100%; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
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


    <!-- Main Content -->
    <div class="main-content">
        <div class="header-bar">
            <h1>Manage Users</h1>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name or role" value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <!-- Students -->
        <section class="user-section">
            <h2>Students</h2>
            <table>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($studentsResult)) : ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($user['student_id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['age']) ?></td>
                        <td><?= htmlspecialchars($user['gender']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['course']) ?></td>
                        <td><?= htmlspecialchars($user['section']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                                <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_students.php">View All Students →</a>
        </section>

        <!-- Customers -->
        <section class="user-section">
            <h2>Customers</h2>
            <table>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Payment Plan</th>
                    <th>Services</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($customersResult)) : ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($user['customer_id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['age']) ?></td>
                        <td><?= htmlspecialchars($user['gender']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['payment_plan']) ?></td>
                        <td><?= htmlspecialchars($user['services']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                                <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_customers.php">View All Customers →</a>
        </section>

        <!-- Faculty -->
        <section class="user-section">
            <h2>Faculty</h2>
            <table>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Faculty ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($facultyResult)) : ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($user['faculty_id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['age']) ?></td>
                        <td><?= htmlspecialchars($user['gender']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['faculty_dept']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                                <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Are you sure you want to delete this faculty member?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_faculty.php">View All Faculty →</a>
        </section>
    </div>
</div>

</body>
</html>

