-- THE VINTAGE STYLE - Database Schema
-- Run this in phpMyAdmin: Import > select this file
-- OR in MySQL CLI: source database.sql

CREATE DATABASE IF NOT EXISTS vintage_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vintage_store;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    phone      VARCHAR(20),
    password   VARCHAR(255) NOT NULL,
    is_admin   TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category    ENUM('jacket','pants','hoodie','shoes') NOT NULL,
    name        VARCHAR(150) NOT NULL,
    price       DECIMAL(8,2) NOT NULL,
    old_price   DECIMAL(8,2),
    image       VARCHAR(255),
    description TEXT,
    slug        VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS orders (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    product_id  INT NOT NULL,
    size        VARCHAR(10),
    quantity    INT DEFAULT 1,
    total       DECIMAL(8,2) NOT NULL,
    card_last4  CHAR(4),
    status      ENUM('pending','paid','shipped','cancelled') DEFAULT 'pending',
    ordered_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Default admin account (password: "password" — CHANGE THIS immediately!)
INSERT INTO users (username, email, password, is_admin) VALUES
('admin','admin@vintagestyle.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- ── Products ──────────────────────────────────────────────────────────────
-- Images match the actual shop HTML files:
--   Jackets  → 74d2f478….avif, 6b05e9b5….avif, 2.avif–8.avif
--   Pants    → 1.1.avif – 1.8.avif
--   Hoodies  → 2.1.avif – 2.8.avif
--   Shoes    → 3.1.avif – 3.8.avif

INSERT INTO products (category, name, price, old_price, image, slug) VALUES

-- JACKETS
('jacket','Vintage Batwing Oversized Jacket',          59.50, 73.00, 'image/74d2f478-2236-448d-9b67-575679fcd332.avif', 'shop1'),
('jacket','Vintage Motorcycle Jacket',                 65.00, 85.00, 'image/6b05e9b5-2070-4cf4-afa1-23793249ec62.avif', 'shop2'),
('jacket',"Women's Vintage Brown Jacket",              72.00, 90.00, 'image/2.avif',  'shop3'),
('jacket',"Men's Classic Vintage Jacket",              78.00, 98.00, 'image/3.avif',  'shop4'),
('jacket','Tie-dye Print Jacket',                      55.00, 70.00, 'image/4.avif',  'shop5'),
('jacket',"Men's Vintage Distressed Black Denim Jacket",68.00,88.00, 'image/5.avif',  'shop6'),
('jacket',"Men's Retro Washed Denim Jacket",           62.00, 80.00, 'image/7.avif',  'shop7'),
('jacket',"Men's New Washed Blue Denim Jacket",        58.00, 75.00, 'image/8.avif',  'shop8'),

-- PANTS
('pants',"Men's Vintage Washed Blue Jeans",            59.50, 73.00, 'image/1.1.avif','shop1'),
('pants','Loose Wide-Leg Jeans with Floral Embroidery',55.00, 68.00, 'image/1.2.avif','shop2'),
('pants',"Straight Loose Men's Jeans",                 52.00, 65.00, 'image/1.3.avif','shop3'),
('pants',"Men's Pure Cotton Straight-Leg Casual Pants",48.00, 60.00, 'image/1.4.avif','shop4'),
('pants','American High Street Vintage Washed Jeans',  63.00, 80.00, 'image/1.5.avif','shop5'),
('pants',"Men's Fashion Vintage Wide-Leg Casual Pants",57.00, 72.00, 'image/1.6.avif','shop6'),
('pants','Vintage Daisy Print Wide-Leg Work Pants',    60.00, 75.00, 'image/1.7.avif','shop7'),
('pants',"Men's Heavy-Duty Vintage Washed Jeans",      65.00, 82.00, 'image/1.8.avif','shop8'),

-- HOODIES
('hoodie','Y2K Unisex Retro Hoodie',                   59.50, 73.00, 'image/2.1.avif','shop1'),
('hoodie','Sweatshirt with Bold Vintage Print',        52.00, 65.00, 'image/2.2.avif','shop2'),
('hoodie','Oversized Vintage Graphic Sweatshirt',      55.00, 70.00, 'image/2.3.avif','shop3'),
('hoodie','Hooded Loose Casual Sweatshirt',            48.00, 60.00, 'image/2.4.avif','shop4'),
('hoodie','Vintage Dollar Bill Print Hoodie',          62.00, 78.00, 'image/2.5.avif','shop5'),
('hoodie','Gothic Portrait Print Pullover',            57.00, 72.00, 'image/2.6.avif','shop6'),
('hoodie',"Women's Y2K Retro Graphic Sweater",         50.00, 63.00, 'image/2.7.avif','shop7'),
('hoodie',"Men's American Vintage Double Zipper Hoodie",68.00,85.00, 'image/2.8.avif','shop8'),

-- SHOES
('shoes',"Couple's Versatile Trendy Sports Casual Shoes",59.50,73.00,'image/3.1.avif','shop1'),
('shoes','Unisex Vintage Skate Shoes Anti-Slip',       54.00, 68.00, 'image/3.2.avif','shop2'),
('shoes',"Men's Casual Retro Lace-Up Loafers",         62.00, 78.00, 'image/3.3.avif','shop3'),
('shoes',"Men's Lightweight Mid-Calf Hiking Boots",    75.00, 95.00, 'image/3.4.avif','shop4'),
('shoes',"Women's Vintage Sneakers",                   49.00, 62.00, 'image/3.5.avif','shop5'),
('shoes',"Four Seasons Outdoor Women's Fashion Shoes", 45.00, 58.00, 'image/3.6.avif','shop6'),
('shoes','Classic American Graffiti Sneakers',         52.00, 66.00, 'image/3.7.avif','shop7'),
('shoes','Derby Shoes Spring Low-Top Vintage',         58.00, 73.00, 'image/3.8.avif','shop8');
