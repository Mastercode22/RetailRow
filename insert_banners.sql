-- This script inserts three sample banners into your database,
-- using the image files that are available in your '/assets/images/banners/' directory.
-- Please import this file into your 'retailrow' database.

USE retailrow;

-- Delete existing banners to avoid duplicates if you run this script multiple times.
-- This is optional, but recommended for a clean slate.
-- DELETE FROM `banners`;

-- Insert three banners
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `sort_order`, `is_active`) VALUES
('SM-SERIES', 'PRE-ORDER NOW', 'banner_1769779628_5979.jpg', '#', 1, 1),
('FLASH SALE', 'Up to -60% OFF', 'banner_1769782555_6218.jpg', '#', 2, 1),
('NEW ARRIVALS', 'Latest Collections', 'banner_1769963795_3375.jpg', '#', 3, 1);

