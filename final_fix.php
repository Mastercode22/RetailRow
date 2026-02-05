<?php
// Final fix script
echo "<h1>RetailRow Final Setup Fix</h1>";

// 1. Test database connection
require_once __DIR__ . '/config/db.php';
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "❌ Database connection failed<br>";
    exit();
}
echo "✅ Database connected<br>";

// 2. Create tables if they don't exist
$create_tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        icon VARCHAR(255),
        is_active BOOLEAN DEFAULT TRUE,
        position INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        category_id INT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        old_price DECIMAL(10,2) NULL,
        discount INT DEFAULT 0,
        image VARCHAR(255),
        stock INT DEFAULT 0,
        is_featured BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS banners (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255),
        subtitle VARCHAR(255),
        image VARCHAR(255),
        link VARCHAR(255),
        position INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        key_name VARCHAR(100) UNIQUE NOT NULL,
        value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($create_tables as $sql) {
    try {
        $db->exec($sql);
        echo "✅ Table created<br>";
    } catch (Exception $e) {
        echo "❌ Table creation error: " . $e->getMessage() . "<br>";
    }
}

// 3. Create admin user with correct password
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$admin_sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')
              ON DUPLICATE KEY UPDATE password = VALUES(password), status = 'active'";

$stmt = $db->prepare($admin_sql);
$stmt->execute(['Admin User', 'admin@retailrow.com', $hash]);

echo "✅ Admin user created/updated<br>";

// 4. Add some sample data
$sample_data = [
    "INSERT IGNORE INTO categories (name, is_active, position) VALUES
    ('Electronics', TRUE, 1), ('Fashion', TRUE, 2), ('Home & Garden', TRUE, 3)",

    "INSERT IGNORE INTO products (name, price, stock, is_featured, is_active) VALUES
    ('Wireless Headphones', 99.99, 50, TRUE, TRUE),
    ('Smart Watch', 199.99, 30, TRUE, TRUE)",

    "INSERT IGNORE INTO settings (key_name, value) VALUES
    ('site_title', 'RetailRow'), ('phone_number', '+1-234-567-8900')"
];

foreach ($sample_data as $sql) {
    try {
        $db->exec($sql);
        echo "✅ Sample data added<br>";
    } catch (Exception $e) {
        echo "❌ Sample data error: " . $e->getMessage() . "<br>";
    }
}

// 5. Test login
require_once __DIR__ . '/config/auth.php';
$auth = new Auth();
$result = $auth->login('admin@retailrow.com', 'admin123');

if ($result['success']) {
    echo "✅ Login test successful<br>";
    echo "🎉 Everything is working!<br>";
    echo "<br><a href='admin/login.php'>Go to Admin Login</a><br>";
    echo "<a href='index.php'>Go to Frontend</a><br>";
} else {
    echo "❌ Login test failed: " . $result['message'] . "<br>";
}
?>