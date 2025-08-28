<?php
// Load Composer autoload
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

// ---------------- Database connection ----------------
$host = 'localhost';
$db   = 'gym_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// ---------------- Twilio credentials ----------------
$twilioSID   = 'YOUR_REAL_TWILIO_SID';
$twilioToken = 'YOUR_REAL_TWILIO_AUTH_TOKEN';
$twilioFrom  = '+1234567890'; // Your Twilio phone number with country code
$twilio = new Client($twilioSID, $twilioToken);

// ---------------- PHPMailer SMTP credentials ----------------
$smtpHost = 'smtp.gmail.com';
$smtpUser = 'your-email@gmail.com';      // Gmail email
$smtpPass = 'your-app-password';         // Gmail App Password
$smtpPort = 587;

// ---------------- Alert configuration ----------------
$alertDays = 7; // send alerts X days before membership expiry
$today = date('Y-m-d');

// ---------------- Fetch expiring members ----------------
// Replace `membership_end` with your actual expiry column name
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE membership_end BETWEEN :today AND DATE_ADD(:today, INTERVAL :days DAY)
");
$stmt->execute(['today' => $today, 'days' => $alertDays]);
$members = $stmt->fetchAll();

if (!$members) {
    echo "✅ No memberships expiring in the next $alertDays days.";
    exit;
}

// ---------------- Send alerts ----------------
foreach ($members as $member) {
    $email = $member['email'];
    $name = $member['name'];
    $phone = $member['phone'];
    $end_date = $member['membership_end']; // change if your column is different

    // ---------------- Email via PHPMailer ----------------
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $smtpPort;

        $mail->setFrom('admin@gym.com', 'Gym Admin');
        $mail->addAddress($email, $name);

        $mail->Subject = "Membership Expiry Alert";
        $mail->Body    = "Dear $name,\n\nThis is a reminder that your gym membership will expire on $end_date.\nPlease renew it to continue enjoying our services.\n\nThank you,\nGym Admin";

        $mail->send();
        echo "✅ Email sent to $email<br>";
    } catch (Exception $e) {
        echo "❌ Email error for $email: " . $mail->ErrorInfo . "<br>";
    }

    // ---------------- SMS via Twilio ----------------
    try {
        // Ensure phone number has country code (+63 if missing)
        if (strpos($phone, '+') !== 0) {
            $phone = '+63' . ltrim($phone, '0');
        }

        $twilio->messages->create($phone, [
            'from' => $twilioFrom,
            'body' => "Hi $name! Your gym membership expires on $end_date. Please renew it soon. - Gym Admin"
        ]);

        echo "✅ SMS sent to $phone<br>";
    } catch (Exception $e) {
        echo "❌ SMS error for $phone: " . $e->getMessage() . "<br>";
    }
}
?>
