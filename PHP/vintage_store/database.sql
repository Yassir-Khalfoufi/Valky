-- ============================================
-- VINTAGE STORE DATABASE SCHEMA
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS vintage_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vintage_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    era VARCHAR(30),           -- e.g. "1970s", "1980s"
    condition_label VARCHAR(30), -- e.g. "Excellent", "Good", "Fair"
    size VARCHAR(20),
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO categories (name, slug) VALUES
('Jackets & Coats', 'jackets'),
('Dresses', 'dresses'),
('Tops & Shirts', 'tops'),
('Pants & Trousers', 'pants'),
('Accessories', 'accessories');

-- Default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@vintagestore.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample products
INSERT INTO products (name, description, price, era, condition_label, size, category_id, image, stock) VALUES
('Levi\'s 501 Denim Jacket', 'Classic stonewashed denim jacket with original buttons and chest pockets. A true icon of American workwear.', 89.00, '1980s', 'Excellent', 'M', 1, NULL, 3),
('Floral Wrap Dress', 'Bohemian midi wrap dress with vibrant floral print. Perfect summer vibes from the flower power era.', 65.00, '1970s', 'Good', 'S', 2, NULL, 1),
('Band Tee – The Clash', 'Original concert tee from The Clash. Minor fading adds authentic charm. Collector\'s item.', 110.00, '1980s', 'Fair', 'L', 3, NULL, 1),
('Corduroy Blazer', 'Rich brown corduroy blazer with elbow patches. Academic and effortlessly cool.', 75.00, '1970s', 'Excellent', 'M', 1, NULL, 2),
('High-Waist Plaid Trousers', 'Bold plaid pattern high-waist trousers. Perfectly pressed with side pockets.', 55.00, '1990s', 'Good', 'S', 4, NULL, 2),
('Oversized Bomber Jacket', 'Olive green satin bomber with ribbed cuffs. Street style royalty.', 120.00, '1990s', 'Excellent', 'XL', 1, NULL, 1),
('Silk Slip Dress', 'Ivory bias-cut silk slip dress. Minimalist 90s chic at its finest.', 95.00, '1990s', 'Excellent', 'M', 2, NULL, 1),
('Graphic Polo Shirt', 'Vintage polo with embroidered logo detail. Preppy and timeless.', 40.00, '1980s', 'Good', 'L', 3, NULL, 4),
('Wide-Leg Jeans', 'True vintage wide-leg denim with original stitching. The silhouette is back.', 70.00, '1970s', 'Good', 'M', 4, NULL, 2),
('Leather Belt', 'Hand-tooled leather belt with brass buckle. Genuine craftsmanship that lasts.', 35.00, '1960s', 'Excellent', 'Universal', 5, NULL, 5),
('Patchwork Denim Vest', 'Custom patchwork denim vest — each patch tells a story. One of a kind.', 80.00, '1970s', 'Good', 'M', 1, NULL, 1),
('Velvet Mini Dress', 'Crushed velvet mini dress in deep burgundy. The definition of 90s glam.', 85.00, '1990s', 'Excellent', 'S', 2, NULL, 1);
