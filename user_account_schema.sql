-- RetailRow User Account System Schema
-- Run this script to upgrade the database

-- 1. Update Users Table
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
ADD COLUMN `username` VARCHAR(50) UNIQUE AFTER `name`,
ADD COLUMN `phone` VARCHAR(20) AFTER `email`,
ADD COLUMN `avatar` VARCHAR(255) DEFAULT 'default_avatar.png' AFTER `password`,
ADD COLUMN `gender` ENUM('male', 'female', 'other') NULL AFTER `avatar`,
ADD COLUMN `dob` DATE NULL AFTER `gender`,
ADD COLUMN `email_verified_at` TIMESTAMP NULL AFTER `status`,
ADD COLUMN `remember_token` VARCHAR(100) NULL AFTER `email_verified_at`;

-- 2. Addresses Table
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `country` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `street` VARCHAR(255) NOT NULL,
  `postal_code` VARCHAR(20),
  `is_default_shipping` BOOLEAN DEFAULT FALSE,
  `is_default_billing` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 3. Wishlist Table
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`)
);

-- 4. Password Resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` VARCHAR(150) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_token` (`token`)
);

-- 5. User Sessions (for security tracking)
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` VARCHAR(255) PRIMARY KEY,
  `user_id` INT NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 6. Payment Methods (Tokenized)
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `provider` VARCHAR(50) NOT NULL, -- e.g., 'stripe', 'paypal'
  `last4` VARCHAR(4),
  `token` VARCHAR(255) NOT NULL,
  `is_default` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 7. Notifications Preferences
CREATE TABLE IF NOT EXISTS `notification_preferences` (
  `user_id` INT PRIMARY KEY,
  `email_orders` BOOLEAN DEFAULT TRUE,
  `email_promos` BOOLEAN DEFAULT TRUE,
  `sms_updates` BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);