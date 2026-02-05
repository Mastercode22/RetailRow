#!/usr/bin/env php
<?php
/**
 * Database Migration Script
 * Run this to update your database schema to the latest version
 * 
 * Usage: php migrate.php
 */

require_once __DIR__ . '/config/db.php';

echo "===========================================\n";
echo "RetailRow Database Migration Tool\n";
echo "===========================================\n\n";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "✓ Connected to database\n\n";
    
    // Check if we need to run migration
    echo "Checking current schema...\n";
    
    $migrations = [
        'add_slug_to_categories' => "
            ALTER TABLE categories 
            ADD COLUMN IF NOT EXISTS slug VARCHAR(150) UNIQUE AFTER name,
            ADD INDEX IF NOT EXISTS idx_slug (slug)
        ",
        
        'add_slug_to_products' => "
            ALTER TABLE products 
            ADD COLUMN IF NOT EXISTS slug VARCHAR(300) UNIQUE AFTER name,
            ADD COLUMN IF NOT EXISTS meta_title VARCHAR(255) AFTER is_active,
            ADD COLUMN IF NOT EXISTS meta_description TEXT AFTER meta_title,
            ADD INDEX IF NOT EXISTS idx_slug (slug)
        ",
        
        'create_pages_table' => "
            CREATE TABLE IF NOT EXISTS pages (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content LONGTEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                is_active BOOLEAN DEFAULT TRUE,
                show_in_footer BOOLEAN DEFAULT FALSE,
                show_in_header BOOLEAN DEFAULT FALSE,
                position INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_active (is_active)
            )
        ",
        
        'create_navigation_menus_table' => "
            CREATE TABLE IF NOT EXISTS navigation_menus (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                location ENUM('header', 'footer', 'mobile', 'utility') NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ",
        
        'create_menu_items_table' => "
            CREATE TABLE IF NOT EXISTS menu_items (
                id INT PRIMARY KEY AUTO_INCREMENT,
                menu_id INT NOT NULL,
                label VARCHAR(100) NOT NULL,
                url VARCHAR(255) NOT NULL,
                page_id INT NULL,
                parent_id INT NULL,
                position INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                target ENUM('_self', '_blank') DEFAULT '_self',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (menu_id) REFERENCES navigation_menus(id) ON DELETE CASCADE,
                FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE SET NULL,
                FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
                INDEX idx_menu (menu_id),
                INDEX idx_parent (parent_id),
                INDEX idx_active (is_active)
            )
        ",
        
        'create_footer_link_groups_table' => "
            CREATE TABLE IF NOT EXISTS footer_link_groups (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(100) NOT NULL,
                position INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ",
        
        'create_footer_links_table' => "
            CREATE TABLE IF NOT EXISTS footer_links (
                id INT PRIMARY KEY AUTO_INCREMENT,
                group_id INT NOT NULL,
                label VARCHAR(100) NOT NULL,
                url VARCHAR(255) NOT NULL,
                page_id INT NULL,
                position INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES footer_link_groups(id) ON DELETE CASCADE,
                FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE SET NULL,
                INDEX idx_group (group_id),
                INDEX idx_active (is_active)
            )
        ",
        
        'add_type_to_settings' => "
            ALTER TABLE settings 
            ADD COLUMN IF NOT EXISTS type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text' AFTER value
        ",
        
        'import_cart_checkout_schema' => 'cart_checkout_db.sql'
    ];
    
    $runCount = 0;
    $skipCount = 0;
    
    foreach ($migrations as $name => $source) {
        echo "Running migration: $name ... ";
        
        try {
            if (is_file($source) && pathinfo($source, PATHINFO_EXTENSION) === 'sql') {
                $sql = file_get_contents($source);
                if ($sql === false) {
                    throw new Exception("Could not read file: $source");
                }
                $db->exec($sql);
                 echo "✓ Done (from file)\n";
            } else {
                 $db->exec($source);
                 echo "✓ Done\n";
            }
            $runCount++;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false || 
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'constraint already exists') !== false) {
                echo "⊘ Skipped (already applied)\n";
                $skipCount++;
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n===========================================\n";
    echo "Migration Summary:\n";
    echo "  - Migrations run: $runCount\n";
    echo "  - Migrations skipped: $skipCount\n";
    echo "===========================================\n\n";
    
    // Generate slugs for existing records
    echo "Generating slugs for existing records...\n";
    
    // Categories
    $stmt = $db->query("SELECT id, name FROM categories WHERE slug IS NULL OR slug = ''");
    $categories = $stmt->fetchAll();
    
    foreach ($categories as $cat) {
        $slug = generateSlug($cat['name'], $db, 'categories');
        $update = $db->prepare("UPDATE categories SET slug = ? WHERE id = ?");
        $update->execute([$slug, $cat['id']]);
        echo "  ✓ Category: {$cat['name']} → $slug\n";
    }
    
    // Products
    $stmt = $db->query("SELECT id, name FROM products WHERE slug IS NULL OR slug = ''");
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $slug = generateSlug($product['name'], $db, 'products');
        $update = $db->prepare("UPDATE products SET slug = ? WHERE id = ?");
        $update->execute([$slug, $product['id']]);
        echo "  ✓ Product: {$product['name']} → $slug\n";
    }
    
    echo "\n✓ Migration completed successfully!\n\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

function generateSlug($text, $db, $table) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    $originalSlug = $slug;
    $counter = 1;
    
    while (true) {
        $query = "SELECT COUNT(*) as count FROM $table WHERE slug = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$slug]);
        
        if ($stmt->fetch()['count'] == 0) {
            break;
        }
        
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}
