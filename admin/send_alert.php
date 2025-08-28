<?php
require __DIR__ . '/vendor/autoload.php'; // Path to Composer autoload

use Twilio\Rest\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = $_POST['email'];
    $name = $_POST['name'];
    $end_date = $_POST['end_date'];
    $phone = $_POST['phone']; // Make sure this is included in your form

    // EMAIL PART ---------------------
    $subject = "Membership Expiry Alert";
    $message = "Dear $name,\n\nThis is a reminder that your gym membership will expire on $end_date.\nPlease renew it to continue enjoying our services.\n\nThank you,\nGym Admin";
    $headers = "From: admin@gym.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "✅ Email sent to $email<br>";
    } else {
        echo "❌ Failed to send email.<br>";
    }

    // SMS PART ----------------------
    $twilioSID = 'YOUR_TWILIO_SID';
    $twilioToken = 'YOUR_TWILIO_AUTH_TOKEN';
    $twilioFrom = 'YOUR_TWILIO_PHONE_NUMBER'; // e.g. +1234567890

    try {
        $twilio = new Client($twilioSID, $twilioToken);

        $twilio->messages->create($phone, [
            'from' => $twilioFrom,
            'body' => "Hi $name! Your gym membership expires on $end_date. Please renew it soon. - Gym Admin"
        ]);

        echo "✅ SMS sent to $phone";
    } catch (Exception $e) {
        echo "❌ SMS error: " . $e->getMessage();
    }
}
?>
