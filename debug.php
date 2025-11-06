<?php
// Debug file to check application routing and files

echo "<h2>Application Debug Information</h2>";

// Check if key files exist
echo "<h3>File Existence Check:</h3>";
$files = [
    'index.php',
    '.htaccess',
    'app/init.php',
    'app/core/Core.php',
    'app/controllers/Home.php',
    'app/controllers/Users.php',
    'config/config.php'
];

foreach($files as $file) {
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? 'EXISTS' : 'MISSING';
    echo "$file: <span style='color:$color'>$status</span><br>";
}

// Check .htaccess content
echo "<h3>.htaccess Content:</h3>";
if(file_exists('.htaccess')) {
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<span style='color:red'>File not found</span>";
}

// Check if mod_rewrite is enabled
echo "<h3>Server Configuration:</h3>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Test if constants are defined
echo "<h3>Testing app/init.php:</h3>";
try {
    require_once 'app/init.php';
    echo "PROJECTROOT: <span style='color:green'>" . (defined('PROJECTROOT') ? PROJECTROOT : 'NOT DEFINED') . "</span><br>";
    echo "URLROOT: <span style='color:green'>" . (defined('URLROOT') ? URLROOT : 'NOT DEFINED') . "</span><br>";
    echo "SITENAME: <span style='color:green'>" . (defined('SITENAME') ? SITENAME : 'NOT DEFINED') . "</span><br>";
} catch(Exception $e) {
    echo "<span style='color:red'>Error: " . $e->getMessage() . "</span>";
}

// Check session
echo "<h3>Session Status:</h3>";
echo "Session Started: " . (session_status() === PHP_SESSION_ACTIVE ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
if(session_status() === PHP_SESSION_ACTIVE) {
    echo "Session ID: " . session_id() . "<br>";
    echo "Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";
}

// Check directory permissions
echo "<h3>Directory Permissions:</h3>";
$dirs = ['storage/imports', 'app', 'config'];
foreach($dirs as $dir) {
    if(file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir);
        $color = $writable ? 'green' : 'orange';
        echo "$dir: Permissions $perms - " . ($writable ? '<span style="color:green">Writable</span>' : '<span style="color:orange">Not Writable</span>') . "<br>";
    } else {
        echo "$dir: <span style='color:red'>NOT FOUND</span><br>";
    }
}

echo "<h3>Test Links:</h3>";
echo '<a href="' . URLROOT . '">Home Page (should redirect)</a><br>';
echo '<a href="' . URLROOT . '/users/login">Login Page</a><br>';
echo '<a href="' . URLROOT . '/test.php">Test Page</a><br>';
?>
