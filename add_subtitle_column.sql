-- This script adds the 'subtitle' and 'button_text' columns to your 'banners' table.
-- This will make your database schema match the enhanced version and prevent future errors.
-- Please import this file into your 'retailrow' database after you have successfully run the insert script.

USE retailrow;

ALTER TABLE `banners`
ADD COLUMN `subtitle` VARCHAR(255) NULL AFTER `title`,
ADD COLUMN `button_text` VARCHAR(100) NULL DEFAULT 'SHOP NOW' AFTER `link`;
