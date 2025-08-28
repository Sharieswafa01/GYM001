<?php
include('../user/db_connection.php');

$search = $_GET['search'] ?? '';
$condition = '';

if (!empty($search)) {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $condition = "AND (faculty_id LIKE '%$safeSearch%' OR first_name LIKE '%$safeSearch%' OR last_name LIKE '%$safeSearch%')";
}

$query = "SELECT * FROM users WHERE role = 'Faculty' $condition";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Faculty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .back-link {
            align-self: flex-start;
            margin-bottom: 20px;
            color: white;
            background-color: #28a745;
            text-decoration: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        .container {
            background-color: white;
            width: 100%;
            max-width: 1300px;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-row h2 {
            font-weight: 700;
            font-size: 1.8rem;
            color: #333;
            user-select: none;
        }

        .search-form {
            display: flex;
            align-items: center;
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
            margin-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1rem;
            vertical-align: middle;
        }

        table th {
            background-color: rgb(232, 230, 230);
            color: black;
            font-weight: 700;
            text-transform: uppercase;
            user-select: none;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            color: white;
            border: none;
        }

        .btn-update {
            background-color: #28a745;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-update:hover,
        .btn-delete:hover {
            background-color: #00ff99;
            color: #000;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .header-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .header-row h2 {
                font-size: 1.4rem;
            }

            .search-form {
                width: 100%;
            }

            .search-form input[type="text"] {
                width: 100%;
            }

            table th, table td {
                font-size: 0.85rem;
                padding: 10px 8px;
            }

            .btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }

            .actions {
                flex-direction: column;
                gap: 6px;
            }
        }
    </style>
</head>
<body>
    <a href="manage_users.php" class="back-link"> ← </a>

    <div class="container">
        <div class="header-row">
            <h2>All Faculty</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name or ID" value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

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
                    <th>Faculty Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($result)) : ?>
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
