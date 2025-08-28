<?php
session_start();
require 'db_connection.php'; // Your actual DB connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch existing admin profile data for pre-filling the form
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = filter_var(trim($_POST['admin_name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['admin_email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['admin_password'];
    $profile_pic = $_FILES['profile_pic'];

    // Array to store errors
    $errors = [];

    // Validate form fields
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }

    // If a new profile picture is uploaded
    if ($profile_pic['name']) {
        // Validate the uploaded image (optional: adjust the validation according to your needs)
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($profile_pic['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_ext), $allowed_ext)) {
            $errors[] = "Invalid image format. Allowed formats: jpg, jpeg, png, gif.";
        }

        // Set a unique file name and move the uploaded image to a folder
        $new_profile_pic = 'uploads/' . uniqid('profile_', true) . '.' . $file_ext;
        if (!move_uploaded_file($profile_pic['tmp_name'], $new_profile_pic)) {
            $errors[] = "Failed to upload profile picture.";
        }
    } else {
        $new_profile_pic = $admin['profile_pic']; // Keep the existing picture if not uploading a new one
    }

    // Update profile if no errors
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE admins SET name = ?, email = ?, password = ?, profile_pic = ? WHERE id = ?");
        $result = $stmt->execute([$name, $email, $hashed_password, $new_profile_pic, $admin_id]);

        if ($result) {
            $_SESSION['message'] = "Profile updated successfully.";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/update_admin_profile.css">
</head>
<body>
    <div class="profile-update-container">
        <h2>Update Your Profile</h2>
        
        <!-- Display errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <form action="update_admin_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="admin_name">Name:</label>
                <input type="text" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="admin_email">Email:</label>
                <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="admin_password">New Password:</label>
                <input type="password" id="admin_password" name="admin_password" required>
            </div>

            <!-- Profile Picture Upload -->
            <div class="form-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                <?php if ($admin['profile_pic']): ?>
                    <div class="current-profile-pic">
                        <img src="<?php echo $admin['profile_pic']; ?>" alt="Profile Picture">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
