<?php
// One-click setup script
require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

echo "<h1>RetailRow Setup</h1>";

// Create tables
$tables_sql = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        icon VARCHAR(255),
        is_active BOOLEAN DEFAULT TRUE,
        position INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
        is_flash_sale BOOLEAN DEFAULT FALSE,
        flash_end_time DATETIME NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )",

    "CREATE TABLE IF NOT EXISTS banners (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255),
        image VARCHAR(255), -- Path to banner image
        link VARCHAR(255), -- URL to link to
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS flash_sales (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        product_id INT,
        discount_percentage INT NOT NULL, -- Percentage discount
        end_date DATETIME NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS homepage_sections (
        id INT PRIMARY KEY AUTO_INCREMENT,
        section_name VARCHAR(100) NOT NULL,
        is_visible BOOLEAN DEFAULT TRUE,
        position INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        key_name VARCHAR(100) UNIQUE NOT NULL,
        value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS carts (
        id varchar(255) NOT NULL,
        user_id int(11) DEFAULT NULL,
        created_at timestamp NOT NULL DEFAULT current_timestamp(),
        updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (id),
        KEY user_id (user_id)
    )",

    "CREATE TABLE IF NOT EXISTS cart_items (
        id int(11) NOT NULL AUTO_INCREMENT,
        cart_id varchar(255) NOT NULL,
        product_id int(11) NOT NULL,
        quantity int(11) NOT NULL DEFAULT 1,
        added_at timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        KEY cart_id (cart_id),
        KEY product_id (product_id),
        FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )"
];

foreach ($tables_sql as $sql) {
    try {
        $db->exec($sql);
        echo "✅ Table created successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error creating table: " . $e->getMessage() . "<br>";
    }
}

// Insert default data
$insert_sql = [
    "INSERT IGNORE INTO users (name, email, password, role) VALUES
    ('Admin User', 'admin@retailrow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')",

    "INSERT IGNORE INTO homepage_sections (section_name, is_visible, position) VALUES
    ('hero_carousel', TRUE, 1),
    ('flash_sales', TRUE, 2),
    ('category_tiles', TRUE, 3),
    ('featured_products', TRUE, 4),
    ('promo_banner', TRUE, 5)",

    "INSERT IGNORE INTO settings (key_name, value) VALUES
    ('site_title', 'RetailRow'),
    ('announcement_text', 'FREE SHIPPING on orders over $50'),
    ('phone_number', '+1-234-567-8900'),
    ('promo_text', 'Get up to 70% off on selected items'),
    ('flash_sale_timer', '24:00:00')",

    "INSERT IGNORE INTO categories (name, icon, is_active, position) VALUES
    ('Electronics', 'assets/images/icons/electronics.png', TRUE, 1),
    ('Fashion', 'assets/images/icons/fashion.png', TRUE, 2),
    ('Home & Garden', 'assets/images/icons/home.png', TRUE, 3),
    ('Sports', 'assets/images/icons/sports.png', TRUE, 4),
    ('Books', 'assets/images/icons/books.png', TRUE, 5),
    ('Beauty', 'assets/images/icons/beauty.png', TRUE, 6)",

    "INSERT IGNORE INTO products (category_id, name, description, price, old_price, discount, image, stock, is_featured) VALUES
    (1, 'Wireless Headphones', 'High-quality wireless headphones with noise cancellation', 99.99, 129.99, 23, 'assets/uploads/headphones.jpg', 50, TRUE),
    (1, 'Smart Watch', 'Latest smartwatch with health monitoring features', 199.99, 249.99, 20, 'assets/uploads/smartwatch.jpg', 30, TRUE),
    (2, 'Running Shoes', 'Comfortable running shoes for all terrains', 79.99, 99.99, 20, 'assets/uploads/shoes.jpg', 100, FALSE),
    (3, 'Coffee Maker', 'Automatic coffee maker with programmable timer', 149.99, 179.99, 17, 'assets/uploads/coffeemaker.jpg', 25, TRUE)",

    "INSERT IGNORE INTO banners (title, image, link, sort_order, is_active) VALUES
    ('New Year Sale', 'assets/uploads/banner1.jpg', '#', 1, TRUE),
    ('Fashion Week', 'assets/uploads/banner2.jpg', '#', 2, TRUE)"
];

foreach ($insert_sql as $sql) {
    try {
        $db->exec($sql);
        echo "✅ Data inserted successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error inserting data: " . $e->getMessage() . "<br>";
    }
}

// Fix admin password
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$query = "UPDATE users SET password = :password WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindValue(':password', $hash);
$stmt->bindValue(':email', 'admin@retailrow.com');
$stmt->execute();

echo "<br>✅ Admin password set to 'admin123'<br>";
echo "<br>🎉 Setup complete!<br>";
echo "<br><a href='admin/login.php'>Go to Admin Login</a><br>";
echo "<a href='index.php'>Go to Frontend</a><br>";
echo "<a href='check_db.php'>Check Database Status</a><br>";
?>