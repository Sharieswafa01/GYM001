<?php
// admin/path_debug.php
error_reporting(E_ALL);
ini_set('display_errors',1);
echo "<pre>__DIR__: " . __DIR__ . "\n";
echo "autoload exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'yes' : 'no') . "\n";
echo "PHPMailer path: " . (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php') ? 'yes' : 'no') . "\n";
echo "realpath autoload: " . realpath(__DIR__ . '/../vendor/autoload.php') . "\n";
echo "</pre>";
