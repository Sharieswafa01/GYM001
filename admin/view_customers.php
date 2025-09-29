<?php 
include('../user/db_connection.php');

$search = $_GET['search'] ?? '';
$condition = '';

if (!empty($search)) {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $condition = "AND (customer_id LIKE '%$safeSearch%' OR first_name LIKE '%$safeSearch%' OR last_name LIKE '%$safeSearch%')";
}

$query = "SELECT * FROM users WHERE role = 'Customer' $condition";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Customers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
   <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #0d1b2a; /* Dark navy */
        padding: 20px;
        color: #e0e6ed;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    a.back-link {
        align-self: flex-start;
        margin-bottom: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 5px;
        background-color: #0077b6; /* Blue button */
        color: white;
        transition: background 0.3s ease;
    }

    a.back-link:hover {
        background-color: #00b4d8; /* Cyan hover */
    }

    .container {
        background: #1b263b; /* Dark panel */
        padding: 20px;
        border-radius: 8px;
        margin: 20px auto;
        max-width: 95%;
        min-height: 80vh;
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        overflow-x: auto;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-row h3 {
        font-size: 1.8rem;
        color: #90e0ef; /* Cyan heading */
    }

    .search-form {
        display: flex;
        align-items: center;
    }

    .search-form input[type="text"] {
        padding: 8px;
        width: 240px;
        border-radius: 6px;
        border: 1px solid #2c2f38;
        background: #14213d;
        color: #e0e6ed;
    }

    .search-form input[type="text"]:focus {
        outline: none;
        border-color: #00b4d8;
        box-shadow: 0 0 5px #00b4d8;
    }

    .search-form button {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        background-color: #0077b6;
        color: white;
        cursor: pointer;
        margin-left: 8px;
        transition: background 0.3s ease;
    }

    .search-form button:hover {
        background-color: #00b4d8;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    table th, table td {
        padding: 12px 14px;
        text-align: left;
        border-bottom: 1px solid #2c2f38;
        font-size: 1rem;
    }

    table th {
        background-color: #14213d;
        font-weight: bold;
        text-transform: uppercase;
        color: #90e0ef;
    }

    table tr:nth-child(even) {
        background-color: #1e2a47;
    }

    .btn {
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        border: none;
        display: inline-block;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-update {
        background-color: #0077b6;
    }

    .btn-update:hover {
        background-color: #00b4d8;
    }

    .btn-delete {
        background-color: #dc3545;
    }

    .btn-delete:hover {
        background-color: #ff6b6b;
    }

    .actions {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
    }

    @media (max-width: 768px) {
        table th, table td {
            font-size: 0.85rem;
        }

        .container h3 {
            font-size: 1.4rem;
        }

        .header-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .search-form {
            width: 100%;
        }

        .search-form input[type="text"] {
            width: 100%;
        }

        .actions {
            flex-direction: column;
            gap: 6px;
        }

        .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

</head>
<body>
    <a href="manage_users.php" class="back-link"> ← </a>

    <div class="container">
        <div class="header-row">
            <h3>All Customers</h3>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name or ID" value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

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
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($result)) : ?>
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
                        <div class="actions">
                            <a href="edit_user.php?id=<?= urlencode($user['id']) ?>" class="btn btn-update">Update</a>
                            <a href="delete_user.php?id=<?= urlencode($user['id']) ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
