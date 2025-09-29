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
    $searchSql = "AND (
        first_name LIKE '%$safeSearch%' 
        OR last_name LIKE '%$safeSearch%' 
        OR role LIKE '%$safeSearch%'
        OR student_id LIKE '%$safeSearch%'
        OR customer_id LIKE '%$safeSearch%'
        OR faculty_id LIKE '%$safeSearch%'
    )";
}


$limit = 5;

// Fetch role-based users
$studentsQuery  = $conn->query("SELECT * FROM users WHERE role = 'Student' $searchSql LIMIT $limit");
$customersQuery = $conn->query("SELECT * FROM users WHERE role = 'Customer' $searchSql LIMIT $limit");
$facultyQuery   = $conn->query("SELECT * FROM users WHERE role = 'Faculty' $searchSql LIMIT $limit");

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
        background-color: #0d1b2a; /* Dark navy background */
        color: #e0e6ed; /* Light text */
    }

    .dashboard-wrapper { display: flex; }

    .sidebar {
        width: 290px;
        background-color: #1b263b; /* Dark sidebar */
        color: #fff;
        height: 100vh;
        padding: 20px;
        position: fixed;
        overflow-y: auto;
        box-shadow: 2px 0 8px rgba(0,0,0,0.5);
    }

    .sidebar .logo {
        text-align: center;
        padding: 30px 0 20px;
        border-bottom: 1px solid #2c2f38;
        margin-bottom: 30px;
    }

    .sidebar .logo h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        color: #3b82f6; /* Cyan accent */
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
        color: #e0e6ed;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-radius: 6px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar .nav ul li a:hover {
        background-color: #2d3748;
        color: #00b4d8; /* Bright cyan on hover */
    }

    .sidebar-logout a {
        color: #e0e6ed;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 60px;
        font-weight: bold;
        font-size: 18px;
        padding: 10px 15px;
        border-radius: 6px;
        transition: background 0.3s, color 0.3s;
    }

    .sidebar-logout a:hover {
        background-color: #2d3748;
        color: #ff6b6b; /* Soft red for logout */
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
        color: #e0e6ed; /* Accent title */
        font-size: 2rem;
    }

    .search-form input[type="text"] {
        padding: 8px;
        width: 240px;
        border-radius: 6px;
        border: 1px solid #2c2f38;
        background: #14213d;
        color: #e0e6ed;
    }

    .search-form button {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        background-color: #0077b6; /* Bright blue */
        color: white;
        cursor: pointer;
    }

    .search-form button:hover {
        background-color: #00b4d8; /* Cyan hover */
    }

    section {
        margin-bottom: 40px;
    }

    .user-section {
        margin-bottom: 60px;
        background: #1b263b;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.4);
    }

    .view-all-link {
        display: block;
        margin-top: 10px;
        text-align: right;
        color: #00b4d8;
        text-decoration: none;
    }

    .view-all-link:hover {
        text-decoration: underline;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #2c2f38;
    }

    thead tr {
        background-color: #334155; /* Blue header */
        color: #fff;
    }

    tbody tr:nth-child(even) {
        background: #14213d;
    }

    tbody tr:nth-child(odd) {
        background: #1f2d3d;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        padding: 6px 0;
    }

    a.update-btn {
        background-color: #0077b6; /* Blue for update */
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
        text-decoration: none;
        transition: background 0.3s;
    }

    a.update-btn:hover {
        background-color: #00b4d8; /* Cyan hover */
    }

    a.delete-btn {
        background-color: #ff6b6b; /* Red for delete */
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
        text-decoration: none;
        transition: background 0.3s;
    }

    a.delete-btn:hover {
        background-color: #d64545;
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
       <!-- Students -->
<section class="user-section">
    <!-- Heading + Download button -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="margin:0;">Students</h2>
        <a href="download_users.php" 
           style="display:inline-block; background:#4CAF50; color:white; padding:10px 18px; 
                  border-radius:8px; text-decoration:none; font-weight:bold; transition:0.3s;">
           <i class="fas fa-download"></i> Download All Users
        </a>
    </div>

    <!-- Students Table -->
    <table>
        <thead>
        <tr>
            <th>No.</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Section</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $counter = 1; while ($user = $studentsQuery->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($user['student_id']) ?></td>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['course']) ?></td>
                <td><?= htmlspecialchars($user['section']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td>
                    <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                    <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Delete this student?');">Delete</a>
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
            <th>Payment Plan</th>
            <th>Services</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $counter = 1; while ($user = $customersQuery->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($user['customer_id']) ?></td>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['payment_plan']) ?></td>
                <td><?= htmlspecialchars($user['services']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td>
                    <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                    <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Delete this customer?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a class="view-all-link" href="view_customers.php">View All Customers →</a>
</section>


<!-- Faculty -->
<section class="user-section">
    <h2>Staff</h2>
    <table>
        <thead>
        <tr>
            <th>No.</th>
            <th>Faculty ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php $counter = 1; while ($user = $facultyQuery->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($user['faculty_id']) ?></td>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['faculty_dept']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td>
                    <a class="update-btn" href="edit_user.php?id=<?= urlencode($user['id']) ?>">Update</a>
                    <a class="delete-btn" href="delete_user.php?id=<?= urlencode($user['id']) ?>" onclick="return confirm('Delete this faculty member?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a class="view-all-link" href="view_faculty.php">View All Staff →</a>
</section>

    </div>
</div>

</body>
</html>

