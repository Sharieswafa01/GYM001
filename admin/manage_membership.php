<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php');

// Handle search input
$search = $_GET['search'] ?? '';
$searchCondition = "";

if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $searchCondition = "WHERE (
        u.first_name LIKE '%$safeSearch%' OR 
        u.last_name LIKE '%$safeSearch%'
    )";
}

$query = "SELECT m.*, u.first_name, u.last_name, u.email, u.phone
          FROM memberships m
          JOIN users u ON m.user_id = u.id
          $searchCondition
          ORDER BY m.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Memberships</title>
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
            padding: 40px 60px;
            flex: 1;
            background-color: #fff;
            overflow-y: auto;
        }

        h1 {
            text-align: center;
            color: #000;
            margin-bottom: 30px;
            font-size: 2rem;
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

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-membership-btn {
            background-color: #28a745;
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            color: #000;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .price {
            font-weight: bold;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .actions a,
        .actions form button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            width: 100px; /* Equal width */
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            transition: background 0.3s, color 0.3s;
            cursor: pointer;
        }

        .actions a[href*="edit_membership"] {
            background-color: #28a745;
            color: white;
        }

        .actions a.delete {
            background-color: #f44336;
            color: white;
        }

        .actions form button {
            background-color: #ffc107;
            border: none;
            color: #000;
        }

        .actions a:hover,
        .actions a.delete:hover,
        .actions form button:hover {
            filter: brightness(90%);
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

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-form {
                margin-top: 10px;
                width: 100%;
            }

            .search-form input[type="text"] {
                width: 100%;
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
            <h1>Manage Memberships</h1>

            <div class="top-bar">
                <a href="add_membership.php" class="add-membership-btn"><i class="fas fa-plus"></i> Add Membership</a>
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search by Name" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>

            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Services</th>
                    <th>Duration</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 1;
                $today = date("Y-m-d");
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $daysRemaining = (strtotime($row['end_date']) - strtotime($today)) / (60 * 60 * 24);
                        $highlight = ($daysRemaining <= 7) ? 'style="background-color: #f9f9f9;"' : '';
                        ?>
                        <tr <?= $highlight ?> >
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['plan_name']) ?></td>
                            <td><?= htmlspecialchars($row['duration']) ?></td>
                            <td><?= htmlspecialchars($row['end_date']) ?></td>
                            <td class="price">₱<?= htmlspecialchars($row['price']) ?></td>
                            <td class="actions">
                                <a href="edit_membership.php?id=<?= $row['id'] ?>"><i class="fas fa-edit"></i> Update</a>
                                <a href="delete_membership.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this plan?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                <?php if ($daysRemaining <= 7): ?>
                                    
                                    <form method="POST" action="send_alert.php">
    <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>">
    <input type="hidden" name="name" value="<?= htmlspecialchars($row['first_name']) ?>">
    <input type="hidden" name="end_date" value="<?= htmlspecialchars($row['end_date']) ?>">
    <input type="hidden" name="phone" value="<?= htmlspecialchars($row['phone']) ?>"> <!-- Added -->
    <button type="submit"><i class="fas fa-bell"></i> Alert</button>
</form>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    endwhile;
                else:
                    ?>
                    <tr><td colspan="9">No membership records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>


