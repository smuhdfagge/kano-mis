<?php
// Simple test file to verify PHP is working
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Test database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'u801881133_mis');
define('DB_PASS', '6$IZuv=C');
define('DB_NAME', 'u801881133_mis');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection: <span style='color:green'>SUCCESS</span><br>";
} catch(PDOException $e) {
    echo "Database connection: <span style='color:red'>FAILED</span><br>";
    echo "Error: " . $e->getMessage();
}
?>
