-- This script inserts three sample banners into your database.
-- It has been corrected to work with your database schema, which does not have a 'subtitle' column.
-- Please import this file into your 'retailrow' database.

USE retailrow;

-- Insert three banners without the subtitle column
INSERT INTO `banners` (`title`, `image`, `link`, `sort_order`, `is_active`) VALUES
('SM-SERIES', 'banner_1769779628_5979.jpg', '#', 1, 1),
('FLASH SALE', 'banner_1769782555_6218.jpg', '#', 2, 1),
('NEW ARRIVALS', 'banner_1769963795_3375.jpg', '#', 3, 1);
