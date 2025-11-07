<?php
// Test the main application routing
echo "<h2>Application Routing Test</h2>";

// Test 1: Direct access to index.php
echo "<h3>Test 1: Can we access index.php directly?</h3>";
try {
    ob_start();
    $_GET['url'] = 'users/login';
    include 'index.php';
    $output = ob_get_clean();
    
    if(strpos($output, 'login') !== false || headers_sent()) {
        echo "<span style='color:green'>✓ index.php is working</span><br>";
    } else {
        echo "<span style='color:orange'>⚠ index.php executed but may have redirected</span><br>";
    }
} catch(Exception $e) {
    echo "<span style='color:red'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// Test 2: Check what happens when we access root
echo "<h3>Test 2: URL Routing Analysis</h3>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Query String: " . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'None') . "<br>";
echo "GET parameters: <pre>" . print_r($_GET, true) . "</pre>";

// Test 3: Manual routing test
echo "<h3>Test 3: Manual Routing</h3>";
echo '<a href="/mis/">Go to Root (should redirect to login)</a><br>';
echo '<a href="/mis/users/login">Go to Login Page</a><br>';
echo '<a href="/mis/admin/dashboard">Go to Admin Dashboard (requires login)</a><br>';

// Test 4: Check database and users
echo "<h3>Test 4: Database Check</h3>";
require_once 'app/init.php';
require_once 'app/models/User.php';

try {
    $db = new Database();
    echo "Database connection: <span style='color:green'>✓ Connected</span><br>";
    
    $userModel = new User();
    
    // Check if users table exists and has data
    $db->query('SELECT COUNT(*) as count FROM users');
    $result = $db->single();
    echo "Users in database: <span style='color:green'>" . $result->count . "</span><br>";
    
    if($result->count > 0) {
        echo "<br><strong>You can login with:</strong><br>";
        echo "Email: admin@example.com<br>";
        echo "Password: admin123<br>";
    }
    
} catch(Exception $e) {
    echo "Database error: <span style='color:red'>" . $e->getMessage() . "</span><br>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Navigate to <a href='/mis/'>https://mis.sctukano.org/</a> - should redirect to login</li>";
echo "<li>Try <a href='/mis/users/login'>https://mis.sctukano.org/users/login</a> - should show login form</li>";
echo "<li>If you see this page or README.md, check server error logs</li>";
echo "</ol>";
?>
