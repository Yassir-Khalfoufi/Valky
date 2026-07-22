# Le Grenier Vintage — Setup Guide

## Requirements
- XAMPP (PHP 8.x, MySQL, Apache)
- Browser

## Installation Steps

### 1. Copy project files
Copy the entire `vintage_store/` folder into:
```
D:\Xampp\htdocs\vintage_store\
```

### 2. Create the database
Open **phpMyAdmin** → go to the **SQL** tab → paste and run the contents of `database.sql`

Or via MySQL CLI:
```bash
mysql -u root -p < database.sql
```

### 3. Configure database credentials
Edit `config/db.php` if needed:
```php
define('DB_USER', 'root');
define('DB_PASS', '');         // your MySQL password
define('DB_NAME', 'vintage_store');
```

### 4. Start XAMPP
- Start **Apache** and **MySQL** in the XAMPP Control Panel

### 5. Open the site
```
http://localhost/vintage_store/
```

---

## Default Admin Account
| Field    | Value               |
|----------|---------------------|
| Email    | admin@vintagestore.com |
| Password | admin123            |

> Change this password immediately after first login (via phpMyAdmin → users table → edit → re-hash with `password_hash()`).

---

## Project Structure
```
vintage_store/
├── config/
│   └── db.php              # PDO connection
├── includes/
│   ├── auth.php            # Session / auth helpers
│   ├── header.php          # Shared HTML header + nav
│   └── footer.php          # Shared HTML footer
├── assets/
│   ├── css/style.css       # Full stylesheet
│   └── js/main.js          # Mobile nav, filters, cart AJAX
├── uploads/                # Product images (auto-created)
├── index.php               # Shop homepage + product grid
├── product.php             # Single product detail page
├── login.php               # Login form
├── register.php            # Registration form
├── logout.php              # Session destroy + redirect
├── cart.php                # Cart view + AJAX qty update
├── admin.php               # Admin panel (add/delete products)
└── database.sql            # Full schema + seed data
```

## Features
- **PDO** database connection with prepared statements (no SQL injection)
- **bcrypt** password hashing
- **Multi-user** auth with role system (admin / user)
- **Product grid** with category filter, era filter, size filter, and live search
- **Cart** with AJAX quantity update (no page reload)
- **Admin panel** — add products with image upload, delete products
- **Flash messages** for all user actions
- **Responsive** design — mobile nav toggle
- **Vintage aesthetic** — Playfair Display + DM Sans, moss/rust/ivory palette
