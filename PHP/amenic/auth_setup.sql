-- auth_setup.sql — À exécuter dans phpMyAdmin (base: cinelist)
USE cinelist;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,           -- bcrypt hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table tokens "Remember me" (cookie persistant)
CREATE TABLE IF NOT EXISTS remember_tokens (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    token      VARCHAR(64)  NOT NULL UNIQUE,    -- hash SHA-256 du cookie
    expires_at DATETIME     NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lier les films à un utilisateur (si tu veux des watchlists séparées par user)
-- Si la colonne n'existe pas encore :
ALTER TABLE films ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL;
ALTER TABLE films ADD CONSTRAINT IF NOT EXISTS fk_films_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Compte de démo : admin / admin123
INSERT IGNORE INTO users (username, email, password)
VALUES ('admin', 'admin@cinelist.local', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uFutB/WK2');
-- mot de passe : password (hash bcrypt de "password" — change-le !)
