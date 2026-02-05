<?php
// Simple database test
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    echo "✅ MySQL connection successful<br>";

    // Try to select database
    $pdo->exec("USE retailrow");
    echo "✅ Database 'retailrow' selected<br>";

    // Check tables
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tables found: " . count($tables) . "<br>";

    // Check admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@retailrow.com']);
    $count = $stmt->fetchColumn();
    echo "✅ Admin user exists: " . ($count > 0 ? 'YES' : 'NO') . "<br>";

    // Fetch categories
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Categories:<br>";
    echo "<pre>";
    print_r($categories);
    echo "</pre>";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "Please make sure MySQL is running in XAMPP<br>";
}
?>