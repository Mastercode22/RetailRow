-- Database Schema for RetailRow eCommerce Platform
-- Created: January 30, 2026

CREATE DATABASE IF NOT EXISTS retailrow;
USE retailrow;

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
CREATE TABLE users (
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
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(255), -- Path to icon image
    is_active BOOLEAN DEFAULT TRUE,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    discount INT DEFAULT 0, -- Percentage discount
    image VARCHAR(255), -- Path to product image
    stock INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_flash_sale BOOLEAN DEFAULT FALSE,
    flash_end_time DATETIME NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Banners table (Hero Slider)
CREATE TABLE banners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    image VARCHAR(255), -- Path to banner image
    link VARCHAR(255), -- URL to link to
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Flash Sales table
CREATE TABLE flash_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    product_id INT,
    discount_percentage INT NOT NULL, -- Percentage discount
    end_date DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Homepage Sections Control
CREATE TABLE homepage_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(100) NOT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Settings table (for global settings like phone numbers, promo text)
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table (future-ready)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL, -- NULL for guest orders
    customer_name VARCHAR(100),
    customer_email VARCHAR(150),
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order Items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL, -- Price at time of order
    discount DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@retailrow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default homepage sections
INSERT INTO homepage_sections (section_name, is_visible, position) VALUES
('hero_carousel', TRUE, 1),
('flash_sales', TRUE, 2),
('category_tiles', TRUE, 3),
('featured_products', TRUE, 4),
('promo_banner', TRUE, 5);

-- Insert default settings
INSERT INTO settings (key_name, value) VALUES
('site_title', 'RetailRow'),
('announcement_text', 'FREE SHIPPING on orders over $50'),
('phone_number', '+1-234-567-8900'),
('promo_text', 'Get up to 70% off on selected items'),
('flash_sale_timer', '24:00:00');

-- Insert sample categories
INSERT INTO categories (name, icon, is_active, position) VALUES
('Electronics', 'assets/images/icons/electronics.png', TRUE, 1),
('Fashion', 'assets/images/icons/fashion.png', TRUE, 2),
('Home & Garden', 'assets/images/icons/home.png', TRUE, 3),
('Sports', 'assets/images/icons/sports.png', TRUE, 4),
('Books', 'assets/images/icons/books.png', TRUE, 5),
('Beauty', 'assets/images/icons/beauty.png', TRUE, 6);

-- Insert sample products
INSERT INTO products (category_id, name, description, price, old_price, discount, image, stock, is_featured) VALUES
(1, 'Wireless Headphones', 'High-quality wireless headphones with noise cancellation', 99.99, 129.99, 23, 'assets/uploads/headphones.jpg', 50, TRUE),
(1, 'Smart Watch', 'Latest smartwatch with health monitoring features', 199.99, 249.99, 20, 'assets/uploads/smartwatch.jpg', 30, TRUE),
(2, 'Running Shoes', 'Comfortable running shoes for all terrains', 79.99, 99.99, 20, 'assets/uploads/shoes.jpg', 100, FALSE),
(3, 'Coffee Maker', 'Automatic coffee maker with programmable timer', 149.99, 179.99, 17, 'assets/uploads/coffeemaker.jpg', 25, TRUE);

-- Insert sample banners
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `sort_order`, `is_active`) VALUES
('SM-SERIES', 'PRE-ORDER NOW', 'banner_1769779242_9027.jpg', '#', 1, 1),
('FLASH SALE', 'Up to -60% OFF', 'banner_1769779268_9832.jpg', '#', 2, 1),
('NEW ARRIVALS', 'Latest Collections', 'banner_1769779545_6461.jpg', '#', 3, 1);