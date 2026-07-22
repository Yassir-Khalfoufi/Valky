-- fix_images.sql
-- Run this if your products show wrong images (e.g. jackets showing pants photos).
-- It reassigns images and names based on the actual shop HTML files.

USE vintage_store;

-- ── JACKETS (images: 74d2f478…, 6b05e9b5…, 2.avif–8.avif) ───────────────
UPDATE products SET image='image/74d2f478-2236-448d-9b67-575679fcd332.avif',
  name='Vintage Batwing Oversized Jacket'              WHERE category='jacket' AND slug='shop1';
UPDATE products SET image='image/6b05e9b5-2070-4cf4-afa1-23793249ec62.avif',
  name='Vintage Motorcycle Jacket'                     WHERE category='jacket' AND slug='shop2';
UPDATE products SET image='image/2.avif',
  name="Women's Vintage Brown Jacket"                  WHERE category='jacket' AND slug='shop3';
UPDATE products SET image='image/3.avif',
  name="Men's Classic Vintage Jacket"                  WHERE category='jacket' AND slug='shop4';
UPDATE products SET image='image/4.avif',
  name='Tie-dye Print Jacket'                          WHERE category='jacket' AND slug='shop5';
UPDATE products SET image='image/5.avif',
  name="Men's Vintage Distressed Black Denim Jacket"   WHERE category='jacket' AND slug='shop6';
UPDATE products SET image='image/7.avif',
  name="Men's Retro Washed Denim Jacket"               WHERE category='jacket' AND slug='shop7';
UPDATE products SET image='image/8.avif',
  name="Men's New Washed Blue Denim Jacket"            WHERE category='jacket' AND slug='shop8';

-- ── PANTS (images: 1.1 – 1.8) ────────────────────────────────────────────
UPDATE products SET image='image/1.1.avif',
  name="Men's Vintage Washed Blue Jeans"               WHERE category='pants' AND slug='shop1';
UPDATE products SET image='image/1.2.avif',
  name='Loose Wide-Leg Jeans with Floral Embroidery'   WHERE category='pants' AND slug='shop2';
UPDATE products SET image='image/1.3.avif',
  name="Straight Loose Men's Jeans"                    WHERE category='pants' AND slug='shop3';
UPDATE products SET image='image/1.4.avif',
  name="Men's Pure Cotton Straight-Leg Casual Pants"   WHERE category='pants' AND slug='shop4';
UPDATE products SET image='image/1.5.avif',
  name='American High Street Vintage Washed Jeans'     WHERE category='pants' AND slug='shop5';
UPDATE products SET image='image/1.6.avif',
  name="Men's Fashion Vintage Wide-Leg Casual Pants"   WHERE category='pants' AND slug='shop6';
UPDATE products SET image='image/1.7.avif',
  name='Vintage Daisy Print Wide-Leg Work Pants'       WHERE category='pants' AND slug='shop7';
UPDATE products SET image='image/1.8.avif',
  name="Men's Heavy-Duty Vintage Washed Jeans"         WHERE category='pants' AND slug='shop8';

-- ── HOODIES (images: 2.1 – 2.8) ──────────────────────────────────────────
UPDATE products SET image='image/2.1.avif',
  name='Y2K Unisex Retro Hoodie'                       WHERE category='hoodie' AND slug='shop1';
UPDATE products SET image='image/2.2.avif',
  name='Sweatshirt with Bold Vintage Print'            WHERE category='hoodie' AND slug='shop2';
UPDATE products SET image='image/2.3.avif',
  name='Oversized Vintage Graphic Sweatshirt'          WHERE category='hoodie' AND slug='shop3';
UPDATE products SET image='image/2.4.avif',
  name='Hooded Loose Casual Sweatshirt'                WHERE category='hoodie' AND slug='shop4';
UPDATE products SET image='image/2.5.avif',
  name='Vintage Dollar Bill Print Hoodie'              WHERE category='hoodie' AND slug='shop5';
UPDATE products SET image='image/2.6.avif',
  name='Gothic Portrait Print Pullover'                WHERE category='hoodie' AND slug='shop6';
UPDATE products SET image='image/2.7.avif',
  name="Women's Y2K Retro Graphic Sweater"             WHERE category='hoodie' AND slug='shop7';
UPDATE products SET image='image/2.8.avif',
  name="Men's American Vintage Double Zipper Hoodie"   WHERE category='hoodie' AND slug='shop8';

-- ── SHOES (images: 3.1 – 3.8) ────────────────────────────────────────────
UPDATE products SET image='image/3.1.avif',
  name="Couple's Versatile Trendy Sports Casual Shoes" WHERE category='shoes' AND slug='shop1';
UPDATE products SET image='image/3.2.avif',
  name='Unisex Vintage Skate Shoes Anti-Slip'          WHERE category='shoes' AND slug='shop2';
UPDATE products SET image='image/3.3.avif',
  name="Men's Casual Retro Lace-Up Loafers"            WHERE category='shoes' AND slug='shop3';
UPDATE products SET image='image/3.4.avif',
  name="Men's Lightweight Mid-Calf Hiking Boots"       WHERE category='shoes' AND slug='shop4';
UPDATE products SET image='image/3.5.avif',
  name="Women's Vintage Sneakers"                      WHERE category='shoes' AND slug='shop5';
UPDATE products SET image='image/3.6.avif',
  name="Four Seasons Outdoor Women's Fashion Shoes"    WHERE category='shoes' AND slug='shop6';
UPDATE products SET image='image/3.7.avif',
  name='Classic American Graffiti Sneakers'            WHERE category='shoes' AND slug='shop7';
UPDATE products SET image='image/3.8.avif',
  name='Derby Shoes Spring Low-Top Vintage'            WHERE category='shoes' AND slug='shop8';

SELECT category, slug, name, image FROM products ORDER BY category, slug;
