<?php session_start(); ?>
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
    <h2>Kindly provide the required details</h2>
    <form id="signup_form" action="signup_process.php" method="POST">
        <!-- Basic Info -->
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required><br><br>

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required><br><br>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <span id="email_error" style="color:red;"></span><br><br>

        <!-- Phone -->
        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone" required>
        <span id="phone_error" style="color:red;"></span><br><br>

        <!-- Role Selection -->
        <label for="role">User Type:</label>
        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="Student">Student</option>
            <option value="Customer">Customer</option>
            <option value="Faculty">Faculty</option>
        </select><br><br>

        <!-- Student Fields -->
        <div id="student_fields" style="display:none;">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id" id="student_id">
            <span id="student_id_error" style="color:red;"></span><br><br>

            <label for="course">Course:</label>
            <input type="text" name="course" id="course"><br><br>

            <label for="section">Section:</label>
            <input type="text" name="section" id="section"><br><br>
        </div>

        <!-- Customer Fields -->
        <div id="customer_fields" style="display:none;">
            <label for="customer_id">Customer ID (Auto-Generated):</label>
            <input type="text" name="customer_id" id="customer_id" readonly><br><br>

            <label for="payment_plan">Select Plan:</label>
            <select name="payment_plan" id="payment_plan">
                <option value="">Select Plan</option>
                <option value="1 Week">1 Week</option>
                <option value="30 Days">30 Days</option>
                <option value="2 Months">2 Months</option>
                <option value="3 Months">3 Months</option>
            </select><br><br>

            <label for="services">Select Services:</label>
            <select name="services" id="services">
                <option value="">Select Services</option>
                <option value="Gym Access">Gym Access</option>
                <option value="Personal Trainer">Personal Trainer</option>
            </select><br><br>
        </div>

        <!-- Faculty Fields -->
        <div id="faculty_fields" style="display:none;">
            <label for="faculty_id">Faculty ID Number:</label>
            <input type="text" name="faculty_id" id="faculty_id"><br><br>

            <label for="faculty_dept">Faculty Department:</label>
            <select name="faculty_dept" id="faculty_dept">
                <option value="">Select Department</option>
                <option value="COT">COT</option>
                <option value="COE">COE</option>
                <option value="CME">CME</option>
                <option value="CEAS">CEAS</option>
            </select><br><br>
        </div>

        <!-- Submit -->
        <input type="submit" value="Register" id="submit_btn">
    </form>

    <p><a href="user_login.php">Back to Login</a></p>
</div>

<!-- Script -->
<script>
    $(document).ready(function () {
        // Role switch logic
        $('#role').on('change', function () {
            let role = $(this).val();
            $('#student_fields, #customer_fields, #faculty_fields').hide();

            if (role === 'Student') {
                $('#student_fields').show();
                $('#customer_id').val('');
            } else if (role === 'Customer') {
                $('#customer_fields').show();
                $('#customer_id').val(Math.floor(1000000 + Math.random() * 9000000));
            } else if (role === 'Faculty') {
                $('#faculty_fields').show();
                $('#customer_id').val('');
            }
        });

        // Check for duplicates
        function checkDuplicate(field, value, errorElement) {
            $.post('check_duplicate.php', { field: field, value: value }, function (response) {
                if (response === 'exists') {
                    $(errorElement).text('This ' + field + ' is already registered.');
                    $('#submit_btn').prop('disabled', true);
                } else {
                    $(errorElement).text('');
                    $('#submit_btn').prop('disabled', false);
                }
            });
        }

        $('#email').on('blur', function () {
            checkDuplicate('email', $(this).val(), '#email_error');
        });

        $('#phone').on('blur', function () {
            checkDuplicate('phone', $(this).val(), '#phone_error');
        });

        $('#student_id').on('blur', function () {
            checkDuplicate('student_id', $(this).val(), '#student_id_error');
        });
    });
</script>
</body>
</html>
