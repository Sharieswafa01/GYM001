<?php
// admin/test_mailer.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>=== PHPMailer Test ===\n";

// Resolve autoload path
$autoloadPath = realpath(__DIR__ . '/../vendor/autoload.php');
echo "autoload path: " . ($autoloadPath ?: 'NOT FOUND') . "\n";

// Check if autoload exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("❌ vendor/autoload.php not found. Run 'composer install' in project root.\n</pre>");
}

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Try using PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    echo "✅ PHPMailer class is available.\n";

    try {
        $mail = new PHPMailer(true);
        echo "✅ PHPMailer object created successfully.\n";
    } catch (Exception $e) {
        echo "❌ Error creating PHPMailer object: " . $e->getMessage() . "\n";
    }

} else {
    echo "❌ PHPMailer class not found. Autoload failed.\n";
}

echo "=== Test complete ===\n</pre>";
