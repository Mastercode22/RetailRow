-- SQL for Cart and Checkout System
-- This script adds tables for cart persistence and enhances the existing orders schema.

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--
CREATE TABLE `carts` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Alter existing `orders` table to add comprehensive checkout fields
--
ALTER TABLE `orders`
  ADD `session_id` VARCHAR(255) NULL AFTER `user_id`,
  ADD `shipping_address` TEXT NULL AFTER `customer_phone`,
  ADD `shipping_city` VARCHAR(100) NULL AFTER `shipping_address`,
  ADD `shipping_region` VARCHAR(100) NULL AFTER `shipping_city`,
  ADD `delivery_notes` TEXT NULL AFTER `shipping_region`,
  ADD `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `delivery_notes`,
  ADD `tax` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `subtotal`,
  ADD `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `tax`,
  ADD `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `shipping_fee`,
  ADD `payment_method` VARCHAR(50) NULL AFTER `total_amount`,
  ADD `payment_status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' AFTER `payment_method`,
  MODIFY `status` ENUM('pending_payment', 'processing', 'shipped', 'delivered', 'cancelled', 'abandoned') NOT NULL DEFAULT 'pending_payment';

-- --------------------------------------------------------

--
-- Alter existing `order_items` table to store historical product data
--
ALTER TABLE `order_items`
  ADD `product_name` VARCHAR(255) NOT NULL AFTER `product_id`,
  ADD `unit_price` DECIMAL(10,2) NOT NULL AFTER `quantity`,
  MODIFY `price` DECIMAL(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED,
  MODIFY `product_id` INT(11) NULL;

-- --------------------------------------------------------

--
-- Foreign key constraints
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
