-- admin_patch.sql
-- Only needed if you ALREADY ran the OLD database.sql before the fix.
-- If you are starting fresh, run database.sql instead.

USE `vintage_store`;

-- ============================================================
-- Add users.is_admin if missing
-- This avoids using "ADD COLUMN IF NOT EXISTS" for better MySQL compatibility.
-- ============================================================

SET @current_db = DATABASE();

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `users` ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`',
        'SELECT ''Column users.is_admin already exists'' AS message'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @current_db
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'is_admin'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- Add products.description if missing
-- ============================================================

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `products` ADD COLUMN `description` TEXT NULL AFTER `image`',
        'SELECT ''Column products.description already exists'' AS message'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @current_db
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'description'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ============================================================
-- Replace old product data with corrected product data
-- ============================================================
-- Note:
-- This deletes/reloads all products.
-- Foreign key checks are temporarily disabled so TRUNCATE will work
-- even if another table references products.

SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

-- TRUNCATE TABLE `products`;

SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;


-- ============================================================
-- Insert corrected products
-- Slugs are now unique.
-- ============================================================

INSERT INTO `products`
(`category`, `name`, `price`, `old_price`, `image`, `description`, `slug`)
VALUES

-- JACKETS
(
    'jacket',
    'Vintage Batwing Oversized Jacket',
    59.50,
    73.00,
    'image/74d2f478-2236-448d-9b67-575679fcd332.avif',
    'Oversized batwing jacket with a relaxed vintage silhouette.',
    'jacket-batwing-oversized'
),
(
    'jacket',
    'Vintage Motorcycle Jacket',
    59.50,
    73.00,
    'image/6b05e9b5-2070-4cf4-afa1-23793249ec62.avif',
    'Motorcycle-inspired vintage jacket with a rugged retro look.',
    'jacket-motorcycle-vintage'
),
(
    'jacket',
    'Women''s Vintage Brown Jacket',
    59.50,
    73.00,
    'image/2.avif',
    'Brown vintage jacket with easy everyday styling.',
    'jacket-womens-brown'
),
(
    'jacket',
    'Men''s Classic Vintage Jacket',
    59.50,
    73.00,
    'image/3.avif',
    'Classic vintage jacket with a timeless casual finish.',
    'jacket-mens-classic'
),
(
    'jacket',
    'Tie-dye Print Jacket for Men',
    59.50,
    73.00,
    'image/4.avif',
    'Statement tie-dye print jacket with bold vintage character.',
    'jacket-tie-dye-print'
),
(
    'jacket',
    'Men''s Vintage Distressed Black Denim Jacket',
    59.50,
    73.00,
    'image/5.avif',
    'Distressed black denim jacket with a worn-in vintage feel.',
    'jacket-distressed-black-denim'
),
(
    'jacket',
    'Men''s Retro Washed Denim Jacket',
    59.50,
    73.00,
    'image/7.avif',
    'Retro washed denim jacket made for casual streetwear outfits.',
    'jacket-retro-washed-denim'
),
(
    'jacket',
    'Men''s New Washed Blue Denim Jacket',
    59.50,
    73.00,
    'image/8.avif',
    'Washed blue denim jacket with a clean vintage-inspired look.',
    'jacket-washed-blue-denim'
),


-- PANTS
(
    'pants',
    'Men''s Vintage Washed Blue Jeans',
    59.50,
    73.00,
    'image/1.1.avif',
    'Washed blue jeans with a classic vintage fit.',
    'pants-washed-blue-jeans'
),
(
    'pants',
    'Loose Wide-Leg Jeans with Floral Embroidery',
    59.50,
    73.00,
    'image/1.2.avif',
    'Wide-leg jeans finished with floral embroidery details.',
    'pants-floral-wide-leg'
),
(
    'pants',
    'Straight Loose Men''s Jeans',
    59.50,
    73.00,
    'image/1.3.avif',
    'Straight loose jeans designed for relaxed everyday wear.',
    'pants-straight-loose-jeans'
),
(
    'pants',
    'Men''s Pure Cotton Straight-Leg Loose-Fit Casual Pants',
    59.50,
    73.00,
    'image/1.4.avif',
    'Pure cotton casual pants with a straight-leg loose fit.',
    'pants-cotton-straight-casual'
),
(
    'pants',
    'American High Street Vintage Washed Graffiti Jeans',
    59.50,
    73.00,
    'image/1.5.avif',
    'High street washed jeans with bold graffiti styling.',
    'pants-graffiti-washed-jeans'
),
(
    'pants',
    'Men''s Fashion Vintage Wide-Leg Casual Pants',
    59.50,
    73.00,
    'image/1.6.avif',
    'Vintage wide-leg casual pants with a fashion streetwear feel.',
    'pants-vintage-wide-leg'
),
(
    'pants',
    'Vintage Daisy Print Wide-Leg Work Pants',
    59.50,
    73.00,
    'image/1.7.avif',
    'Wide-leg work pants featuring a retro daisy print design.',
    'pants-daisy-work'
),
(
    'pants',
    'Men''s Heavy-Duty Vintage Washed Jeans',
    59.50,
    73.00,
    'image/1.8.avif',
    'Heavy-duty washed jeans with durable vintage styling.',
    'pants-heavy-duty-washed-jeans'
),


-- HOODIES
(
    'hoodie',
    'Y2K Unisex Retro Hoodie',
    59.50,
    73.00,
    'image/2.1.avif',
    'Unisex Y2K hoodie with a retro streetwear look.',
    'hoodie-y2k-unisex-retro'
),
(
    'hoodie',
    'Sweatshirt with Bold Vintage Print',
    59.50,
    73.00,
    'image/2.2.avif',
    'Vintage-print sweatshirt with bold graphic detail.',
    'hoodie-bold-vintage-print'
),
(
    'hoodie',
    'Oversized Vintage Graphic Sweatshirt',
    59.50,
    73.00,
    'image/2.3.avif',
    'Oversized graphic sweatshirt with a vintage-inspired finish.',
    'hoodie-oversized-graphic'
),
(
    'hoodie',
    'Hooded Loose Casual Sweatshirt',
    59.50,
    73.00,
    'image/2.4.avif',
    'Loose casual hooded sweatshirt made for comfortable daily wear.',
    'hoodie-loose-casual'
),
(
    'hoodie',
    'Vintage Dollar Bill Print Hooded Sweatshirt',
    59.50,
    73.00,
    'image/2.5.avif',
    'Hooded sweatshirt featuring a vintage dollar bill print.',
    'hoodie-dollar-bill-print'
),
(
    'hoodie',
    'Gothic Portrait Print Pullover',
    59.50,
    73.00,
    'image/2.6.avif',
    'Pullover sweatshirt with a gothic portrait graphic.',
    'hoodie-gothic-portrait'
),
(
    'hoodie',
    'Women''s Y2K Retro Graphic Sweater',
    59.50,
    73.00,
    'image/2.7.avif',
    'Y2K retro graphic sweater with a soft casual style.',
    'hoodie-womens-y2k-graphic'
),
(
    'hoodie',
    'Men''s American Vintage Double Zipper Hoodie',
    59.50,
    73.00,
    'image/2.8.avif',
    'Double zipper hoodie with American vintage streetwear styling.',
    'hoodie-double-zipper'
),


-- SHOES
(
    'shoes',
    'Couple''s Versatile Trendy Sports Casual Shoes',
    59.50,
    73.00,
    'image/3.1.avif',
    'Versatile sports casual shoes with a trendy everyday design.',
    'shoes-trendy-sports-casual'
),
(
    'shoes',
    'Unisex Vintage Skate Shoes Anti-Slip Thick Sole',
    59.50,
    73.00,
    'image/3.2.avif',
    'Unisex vintage skate shoes with anti-slip thick soles.',
    'shoes-vintage-skate-thick-sole'
),
(
    'shoes',
    'Men''s Casual Retro Lace-Up Loafers',
    59.50,
    73.00,
    'image/3.3.avif',
    'Casual retro loafers with a lace-up design.',
    'shoes-retro-lace-up-loafers'
),
(
    'shoes',
    'Men''s Lightweight Mid-Calf Hiking Boots',
    59.50,
    73.00,
    'image/3.4.avif',
    'Lightweight mid-calf hiking boots for outdoor casual outfits.',
    'shoes-lightweight-hiking-boots'
),
(
    'shoes',
    'Women''s Vintage Sneakers',
    59.50,
    73.00,
    'image/3.5.avif',
    'Vintage sneakers with a simple everyday fashion look.',
    'shoes-womens-vintage-sneakers'
),
(
    'shoes',
    'Four Seasons Outdoor Women''s Fashion Shoes',
    59.50,
    73.00,
    'image/3.6.avif',
    'Outdoor fashion shoes designed for four-season wear.',
    'shoes-outdoor-womens-fashion'
),
(
    'shoes',
    'Jifffly Classic American Graffiti Sneakers',
    59.50,
    73.00,
    'image/3.7.avif',
    'Classic American-style sneakers with graffiti details.',
    'shoes-graffiti-sneakers'
),
(
    'shoes',
    'Derby Shoes Spring Low-Top Men''s Vintage',
    59.50,
    73.00,
    'image/3.8.avif',
    'Low-top vintage derby shoes with a clean spring style.',
    'shoes-derby-low-top-vintage'
);


-- ============================================================
-- Set admin flag on existing admin user
-- ============================================================

UPDATE `users`
SET `is_admin` = 1
WHERE `username` = 'admin';


SELECT 'admin_patch.sql completed successfully' AS `status`;