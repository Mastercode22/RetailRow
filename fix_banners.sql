-- This script updates the banner images to match the files available in the filesystem.
-- Please import this file into your 'retailrow' database.

USE retailrow;

-- Assuming the banner IDs are 1, 2, and 3 for the sample banners.
-- If your IDs are different, you may need to adjust them.

UPDATE `banners` SET `image` = 'banner_1769779628_5979.jpg' WHERE `id` = 1;
UPDATE `banners` SET `image` = 'banner_1769782555_6218.jpg' WHERE `id` = 2;
UPDATE `banners` SET `image` = 'banner_1769963795_3375.jpg' WHERE `id` = 3;
