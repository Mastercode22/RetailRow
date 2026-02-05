SET FOREIGN_KEY_CHECKS = 0;

-- Enhanced Database Schema for RetailRow eCommerce Platform
-- Created: January 31, 2026
-- This schema provides full CMS capabilities for dynamic content management

CREATE DATABASE IF NOT EXISTS retailrow;
USE retailrow;

DROP TABLE IF EXISTS `footer_links`;
DROP TABLE IF EXISTS `footer_link_groups`;
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `navigation_menus`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `homepage_sections`;
DROP TABLE IF EXISTS `flash_sales`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- Users table (Admin & Staff)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    icon VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(300) UNIQUE NOT NULL,
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
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured)
);

-- Banners table (Hero Slider)
CREATE TABLE IF NOT EXISTS banners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    image VARCHAR(255),
    link VARCHAR(255),
    button_text VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_order (sort_order)
);

-- Flash Sales table
CREATE TABLE IF NOT EXISTS flash_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    product_id INT,
    discount_percentage INT NOT NULL,
    end_date DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_active (is_active),
    INDEX idx_product (product_id)
);

-- Pages table (for dynamic page management)
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
);

-- Navigation Menus table
CREATE TABLE IF NOT EXISTS navigation_menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    location ENUM('header', 'footer', 'mobile', 'utility') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Menu Items table
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
);

-- Footer Links Groups
CREATE TABLE IF NOT EXISTS footer_link_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Footer Links
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
);

-- Homepage Sections Control
CREATE TABLE IF NOT EXISTS homepage_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    is_visible BOOLEAN DEFAULT TRUE,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Settings table (for global settings)
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (key_name)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    customer_name VARCHAR(100),
    customer_email VARCHAR(150),
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@retailrow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample banners
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `sort_order`, `is_active`) VALUES
('SM-SERIES', 'PRE-ORDER NOW', 'banner_1769779242_9027.jpg', '#', 1, 1),
('FLASH SALE', 'Up to -60% OFF', 'banner_1769779268_9832.jpg', '#', 2, 1),
('NEW ARRIVALS', 'Latest Collections', 'banner_1769779545_6461.jpg', '#', 3, 1);

-- Insert default homepage sections
INSERT INTO homepage_sections (section_name, title, is_visible, position) VALUES
('hero_carousel', 'Hero Carousel', TRUE, 1),
('flash_sales', 'Flash Sales', TRUE, 2),
('category_tiles', 'Shop by Category', TRUE, 3),
('featured_products', 'Featured Products', TRUE, 4),
('promo_banner', 'Promo Banner', TRUE, 5)
ON DUPLICATE KEY UPDATE section_name=section_name;

-- Insert default settings
INSERT INTO settings (key_name, value, type) VALUES
('site_title', 'RetailRow — Shop Quality Products at the Best Prices in Ghana', 'text'),
('site_description', 'Your one-stop shop for quality products in Ghana', 'text'),
('announcement_text', 'If ibi love, igo show for your cart 💕', 'text'),
('phone_number', '030 274 0642', 'text'),
('whatsapp_number', '233000000000', 'text'),
('promo_text', 'Get up to 70% off on selected items', 'text'),
('flash_sale_timer', '24:00:00', 'text'),
('currency_symbol', 'GH₵', 'text'),
('free_shipping_threshold', '50', 'number'),
('enable_cart', 'true', 'boolean'),
('enable_wishlist', 'true', 'boolean'),
('footer_copyright', '© 2026 RetailRow. All Rights Reserved.', 'text'),
('social_facebook', 'https://facebook.com/retailrow', 'text'),
('social_twitter', 'https://twitter.com/retailrow', 'text'),
('social_instagram', 'https://instagram.com/retailrow', 'text')
ON DUPLICATE KEY UPDATE key_name=key_name;

-- Insert navigation menus
INSERT INTO navigation_menus (name, location, is_active) VALUES
('Main Header Menu', 'header', TRUE),
('Footer Menu 1', 'footer', TRUE),
('Footer Menu 2', 'footer', TRUE),
('Utility Menu', 'utility', TRUE),
('Mobile Menu', 'mobile', TRUE)
ON DUPLICATE KEY UPDATE name=name;

-- Insert default pages
INSERT INTO pages (title, slug, content, meta_title, meta_description, is_active, show_in_footer, position) VALUES
('About Us', 'about', '<h1>About RetailRow</h1><p>Welcome to RetailRow, your trusted online marketplace in Ghana.</p>', 'About RetailRow', 'Learn more about RetailRow and our mission', TRUE, TRUE, 1),
('Contact Us', 'contact', '<h1>Contact Us</h1><p>Get in touch with our customer support team.</p>', 'Contact RetailRow', 'Contact our customer support team', TRUE, TRUE, 2),
('Privacy Policy', 'privacy', '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>', 'Privacy Policy', 'RetailRow privacy policy', TRUE, TRUE, 3),
('Terms and Conditions', 'terms', '<h1>Terms and Conditions</h1><p>Terms of use for RetailRow platform.</p>', 'Terms and Conditions', 'RetailRow terms and conditions', TRUE, TRUE, 4),
('Returns & Refunds', 'returns', '<h1>Returns & Refunds</h1><p>Our return and refund policy.</p>', 'Returns Policy', 'RetailRow returns and refunds policy', TRUE, TRUE, 5),
('Shipping Info', 'shipping', '<h1>Shipping Information</h1><p>Shipping details and delivery times.</p>', 'Shipping Info', 'Shipping and delivery information', TRUE, TRUE, 6),
('FAQ', 'faq', '<h1>Frequently Asked Questions</h1><p>Find answers to common questions.</p>', 'FAQ', 'Frequently asked questions', TRUE, TRUE, 7),
('Track Order', 'track-order', '<h1>Track Your Order</h1><p>Enter your order number to track your shipment.</p>', 'Track Order', 'Track your order status', TRUE, FALSE, 8),
('Help Center', 'help', '<h1>Help Center</h1><p>How can we help you today?</p>', 'Help Center', 'Get help and support', TRUE, FALSE, 9)
ON DUPLICATE KEY UPDATE title=title;

-- Insert Footer Link Groups
INSERT INTO footer_link_groups (title, position, is_active) VALUES
('Customer Service', 1, TRUE),
('Company', 2, TRUE),
('Quick Links', 3, TRUE),
('Legal', 4, TRUE)
ON DUPLICATE KEY UPDATE title=title;

-- Get page IDs for linking
SET @about_page_id = (SELECT id FROM pages WHERE slug = 'about' LIMIT 1);
SET @contact_page_id = (SELECT id FROM pages WHERE slug = 'contact' LIMIT 1);
SET @privacy_page_id = (SELECT id FROM pages WHERE slug = 'privacy' LIMIT 1);
SET @terms_page_id = (SELECT id FROM pages WHERE slug = 'terms' LIMIT 1);
SET @returns_page_id = (SELECT id FROM pages WHERE slug = 'returns' LIMIT 1);
SET @shipping_page_id = (SELECT id FROM pages WHERE slug = 'shipping' LIMIT 1);
SET @faq_page_id = (SELECT id FROM pages WHERE slug = 'faq' LIMIT 1);
SET @track_page_id = (SELECT id FROM pages WHERE slug = 'track-order' LIMIT 1);
SET @help_page_id = (SELECT id FROM pages WHERE slug = 'help' LIMIT 1);

-- Get menu IDs
SET @utility_menu_id = (SELECT id FROM navigation_menus WHERE location = 'utility' LIMIT 1);
SET @footer_menu_1_id = (SELECT id FROM navigation_menus WHERE name = 'Footer Menu 1' LIMIT 1);
SET @footer_menu_2_id = (SELECT id FROM navigation_menus WHERE name = 'Footer Menu 2' LIMIT 1);

-- Insert utility menu items
INSERT INTO menu_items (menu_id, label, url, page_id, position, is_active) VALUES
(@utility_menu_id, 'Sell on RetailRow', '/sell', NULL, 1, TRUE),
(@utility_menu_id, 'RetailRow Express', '/express', NULL, 2, TRUE),
(@utility_menu_id, 'Customer Care', '/contact', @contact_page_id, 3, TRUE)
ON DUPLICATE KEY UPDATE label=label;

-- Get footer group IDs
SET @customer_service_group = (SELECT id FROM footer_link_groups WHERE title = 'Customer Service' LIMIT 1);
SET @company_group = (SELECT id FROM footer_link_groups WHERE title = 'Company' LIMIT 1);
SET @quick_links_group = (SELECT id FROM footer_link_groups WHERE title = 'Quick Links' LIMIT 1);
SET @legal_group = (SELECT id FROM footer_link_groups WHERE title = 'Legal' LIMIT 1);

-- Insert Footer Links
INSERT INTO footer_links (group_id, label, url, page_id, position, is_active) VALUES
-- Customer Service Group
(@customer_service_group, 'Help Center', '/help', @help_page_id, 1, TRUE),
(@customer_service_group, 'Contact Us', '/contact', @contact_page_id, 2, TRUE),
(@customer_service_group, 'Track Order', '/track-order', @track_page_id, 3, TRUE),
(@customer_service_group, 'Returns & Refunds', '/returns', @returns_page_id, 4, TRUE),
(@customer_service_group, 'Shipping Info', '/shipping', @shipping_page_id, 5, TRUE),
(@customer_service_group, 'FAQ', '/faq', @faq_page_id, 6, TRUE),

-- Company Group
(@company_group, 'About Us', '/about', @about_page_id, 1, TRUE),
(@company_group, 'Careers', '/careers', NULL, 2, TRUE),
(@company_group, 'Press', '/press', NULL, 3, TRUE),
(@company_group, 'Blog', '/blog', NULL, 4, TRUE),

-- Quick Links Group
(@quick_links_group, 'Shop All', '/products', NULL, 1, TRUE),
(@quick_links_group, 'Categories', '/categories', NULL, 2, TRUE),
(@quick_links_group, 'Flash Sales', '/flash-sales', NULL, 3, TRUE),
(@quick_links_group, 'My Account', '/account', NULL, 4, TRUE),

-- Legal Group
(@legal_group, 'Privacy Policy', '/privacy', @privacy_page_id, 1, TRUE),
(@legal_group, 'Terms & Conditions', '/terms', @terms_page_id, 2, TRUE),
(@legal_group, 'Cookie Policy', '/cookies', NULL, 3, TRUE)
ON DUPLICATE KEY UPDATE label=label;

SET FOREIGN_KEY_CHECKS = 1;
