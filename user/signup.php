<?php
session_start();
include('db_connection.php'); // Make sure this connects to gym_management

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $age        = intval($_POST['age']);
    $gender     = $_POST['gender'];
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $role       = $_POST['role'];

    $waiver     = isset($_POST['waiver']) ? 1 : 0;
    $waiver_date = $waiver ? date('Y-m-d H:i:s') : NULL;

    // Role-based fields
    $student_id = ($role === 'Student') ? trim($_POST['student_id']) : NULL;
    $course     = ($role === 'Student') ? trim($_POST['course']) : NULL;
    $section    = ($role === 'Student') ? trim($_POST['section']) : NULL;

    $customer_id  = ($role === 'Customer') ? trim($_POST['customer_id']) : NULL;
    $payment_plan = ($role === 'Customer') ? trim($_POST['payment_plan']) : NULL;
    $services     = ($role === 'Customer') ? trim($_POST['services']) : NULL;

    $faculty_id   = ($role === 'Faculty') ? trim($_POST['faculty_id']) : NULL;
    $faculty_dept = ($role === 'Faculty') ? trim($_POST['faculty_dept']) : NULL;

    // Insert user info into users table
    $sql = "INSERT INTO users (
        first_name, last_name, age, gender, email, phone, role,
        waiver_signed, waiver_date, student_id, course, section,
        customer_id, payment_plan, services, faculty_id, faculty_dept, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssissssisssssssss",
        $first_name, $last_name, $age, $gender, $email, $phone, $role,
        $waiver, $waiver_date,
        $student_id, $course, $section,
        $customer_id, $payment_plan, $services,
        $faculty_id, $faculty_dept
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful!";
        header("Location: user_login.php");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Sign Up - CTU Danao Gym</title>
<link rel="stylesheet" href="css/user_signup.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <h2>User Sign Up</h2>

    <?php if(isset($_SESSION['success'])) { echo "<p style='color:green;'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>

    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" required><br><br>

        <label>Age:</label>
        <input type="number" name="age" required><br><br>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option>Male</option>
            <option>Female</option>
        </select><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Phone:</label>
        <input type="text" name="phone" required><br><br>

        <label>User Type:</label>
        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="Student">Student</option>
            <option value="Customer">Customer</option>
            <option value="Faculty">Faculty</option>
        </select><br><br>

        <div id="student_fields" style="display:none;">
            <label>Student ID:</label>
            <input type="text" name="student_id"><br><br>
            <label>Course:</label>
            <input type="text" name="course"><br><br>
            <label>Section:</label>
            <input type="text" name="section"><br><br>
        </div>

        <div id="customer_fields" style="display:none;">
            <label>Customer ID (Auto):</label>
            <input type="text" name="customer_id" id="customer_id" readonly><br><br>
            <label>Plan:</label>
            <select name="payment_plan">
                <option value="">Select Plan</option>
                <option>1 Day</option>
                <option>1 Week</option>
                <option>1 Month</option>
                <option>3 Months</option>
                <option>6 Months</option>
                <option>1 Year</option>
            </select><br><br>
            <label>Services:</label>
            <select name="services">
                <option value="">Select Services</option>
                <option>Gym Access</option>
                <option>Personal Trainer</option>
            </select><br><br>
        </div>

        <div id="faculty_fields" style="display:none;">
            <label>Faculty ID:</label>
            <input type="text" name="faculty_id"><br><br>
            <label>Department:</label>
            <input type="text" name="faculty_dept"><br><br>
        </div>

        <!-- ✅ Waiver moved to bottom -->
        <label><input type="checkbox" name="waiver"> I have submitted my waiver.</label><br><br>

        <input type="submit" value="Register">
    </form>

    <p><a href="user_login.php">Back to Login</a></p>
</div>

<script>
$('#role').on('change', function() {
    $('#student_fields, #customer_fields, #faculty_fields').hide();
    let role = $(this).val();
    if (role === 'Student') $('#student_fields').show();
    if (role === 'Customer') {
        $('#customer_fields').show();
        $('#customer_id').val(Math.floor(1000000 + Math.random() * 9000000));
    }
    if (role === 'Faculty') $('#faculty_fields').show();
});
</script>
</body>
</html>
