<?php
// Test database connection
require_once __DIR__ . '/config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "✅ Database connection successful!<br>";

    // Test query
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Users table exists with {$result['count']} records<br>";

    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "✅ Products table exists with {$result['count']} records<br>";

    $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "✅ Categories table exists with {$result['count']} records<br>";

    echo "<br>🎉 Setup complete! You can now:<br>";
    echo "- Visit <a href='index.php'>Frontend Homepage</a><br>";
    echo "- Visit <a href='admin/login.php'>Admin Login</a> (admin@retailrow.com / admin123)<br>";

} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config/db.php<br>";
}
?>