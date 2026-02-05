#!/usr/bin/env php
<?php
/**
 * RetailRow Setup Verification Script
 * Checks if everything is configured correctly
 * 
 * Usage: php setup-check.php
 */

echo "===========================================\n";
echo "RetailRow Setup Verification\n";
echo "===========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check 1: PHP Version
echo "1. Checking PHP version... ";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✓ " . PHP_VERSION . "\n";
    $success[] = "PHP version is compatible";
} else {
    echo "✗ " . PHP_VERSION . " (requires 7.4+)\n";
    $errors[] = "PHP version too old";
}

// Check 2: Required PHP extensions
echo "2. Checking required PHP extensions...\n";
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'fileinfo'];

foreach ($required_extensions as $ext) {
    echo "   - $ext: ";
    if (extension_loaded($ext)) {
        echo "✓\n";
    } else {
        echo "✗ (missing)\n";
        $errors[] = "Missing PHP extension: $ext";
    }
}

// Check 3: Config file
echo "3. Checking configuration files...\n";
echo "   - config/db.php: ";
if (file_exists(__DIR__ . '/config/db.php')) {
    echo "✓\n";
    $success[] = "Database config exists";
} else {
    echo "✗ (missing)\n";
    $errors[] = "Database configuration file missing";
}

// Check 4: Database connection
echo "4. Testing database connection... ";
try {
    require_once __DIR__ . '/config/db.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "✓\n";
    $success[] = "Database connection successful";
    
    // Check 5: Required tables
    echo "5. Checking database tables...\n";
    $required_tables = [
        'users', 'products', 'categories', 'banners', 
        'flash_sales', 'settings', 'pages', 'navigation_menus', 
        'menu_items', 'footer_link_groups', 'footer_links'
    ];
    
    foreach ($required_tables as $table) {
        echo "   - $table: ";
        try {
            $stmt = $db->query("SELECT 1 FROM $table LIMIT 1");
            echo "✓\n";
        } catch (PDOException $e) {
            echo "✗ (missing)\n";
            $errors[] = "Table missing: $table";
        }
    }
    
    // Check 6: Admin user
    echo "6. Checking admin user... ";
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "✓ ({$result['count']} admin user(s))\n";
        $success[] = "Admin user exists";
    } else {
        echo "✗ (no admin users)\n";
        $errors[] = "No admin users found";
    }
    
} catch (Exception $e) {
    echo "✗\n";
    echo "   Error: " . $e->getMessage() . "\n";
    $errors[] = "Database connection failed";
}

// Check 7: File permissions
echo "7. Checking file permissions...\n";
$writable_dirs = [
    'assets/uploads',
    'assets/images/banners'
];

foreach ($writable_dirs as $dir) {
    echo "   - $dir: ";
    $path = __DIR__ . '/' . $dir;
    
    if (!file_exists($path)) {
        @mkdir($path, 0755, true);
    }
    
    if (is_writable($path)) {
        echo "✓ (writable)\n";
    } else {
        echo "✗ (not writable)\n";
        $warnings[] = "Directory not writable: $dir";
    }
}

// Check 8: API files
echo "8. Checking API endpoints...\n";
$api_files = [
    'products.php', 'categories.php', 'banners.php', 
    'flash-sales.php', 'settings.php', 'pages.php',
    'navigation.php', 'footer-links.php'
];

foreach ($api_files as $file) {
    echo "   - api/$file: ";
    if (file_exists(__DIR__ . '/api/' . $file)) {
        echo "✓\n";
    } else {
        echo "✗ (missing)\n";
        $errors[] = "API file missing: $file";
    }
}

// Check 9: JavaScript files
echo "9. Checking JavaScript files...\n";
$js_files = ['api.js', 'app.js', 'main.js'];

foreach ($js_files as $file) {
    echo "   - js/$file: ";
    if (file_exists(__DIR__ . '/js/' . $file)) {
        echo "✓\n";
    } else {
        echo "✗ (missing)\n";
        $errors[] = "JavaScript file missing: $file";
    }
}

// Summary
echo "\n===========================================\n";
echo "Setup Verification Summary\n";
echo "===========================================\n";

if (count($errors) === 0) {
    echo "✓ All checks passed!\n";
    echo "\nYour RetailRow installation is ready.\n";
    echo "\nNext steps:\n";
    echo "1. Visit /admin/login.php\n";
    echo "2. Login with: admin@retailrow.com / admin123\n";
    echo "3. Change your password immediately\n";
    echo "4. Start adding your content\n";
} else {
    echo "✗ Setup incomplete\n";
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

if (count($warnings) > 0) {
    echo "\nWarnings:\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
}

echo "\n";
