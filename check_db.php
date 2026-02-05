<?php
// Database status check
require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

echo "<h1>Database Status Check</h1>";

// Check if retailrow database exists
try {
    $stmt = $db->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    echo "✅ Connected to database: {$result['current_db']}<br>";
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
}

// Check tables
$tables = ['users', 'categories', 'products', 'banners', 'flash_sales', 'homepage_sections', 'settings', 'carts', 'cart_items'];
foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "✅ Table '$table' exists with {$result['count']} records<br>";
    } catch (Exception $e) {
        echo "❌ Table '$table' error: " . $e->getMessage() . "<br>";
    }
}

// Check admin user
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@retailrow.com']);
    $user = $stmt->fetch();

    if ($user) {
        echo "<br>✅ Admin user found:<br>";
        echo "- Email: {$user['email']}<br>";
        echo "- Role: {$user['role']}<br>";
        echo "- Status: {$user['status']}<br>";
        echo "- Password hash length: " . strlen($user['password']) . "<br>";
    } else {
        echo "❌ Admin user not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking admin user: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/login.php'>Go to Admin Login</a>";
?>