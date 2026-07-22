-- auth_setup.sql — À exécuter dans phpMyAdmin (base: cinelist)
USE cinelist;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table tokens "Remember me"
CREATE TABLE IF NOT EXISTS remember_tokens (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT         NOT NULL,
    token      VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME    NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Migrer affiche_url → affiche BLOB (pour les installations existantes)
-- Étape 1 : ajouter les nouvelles colonnes si elles n'existent pas
SET @col1 := (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'films' AND COLUMN_NAME = 'affiche');
SET @sql3 := IF(@col1 = 0,
    'ALTER TABLE films ADD COLUMN affiche LONGBLOB, ADD COLUMN affiche_mime VARCHAR(50)',
    'SELECT "affiche columns already exist"');
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

-- Étape 2 : supprimer affiche_url si elle existe encore
SET @col2 := (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'films' AND COLUMN_NAME = 'affiche_url');
SET @sql4 := IF(@col2 > 0,
    'ALTER TABLE films DROP COLUMN affiche_url',
    'SELECT "affiche_url already removed"');
PREPARE stmt4 FROM @sql4; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

SET @exist := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'films'
      AND COLUMN_NAME  = 'user_id'
);
SET @sql := IF(@exist = 0,
    'ALTER TABLE films ADD COLUMN user_id INT DEFAULT NULL',
    'SELECT "user_id already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ajouter la clé étrangère si elle n'existe pas déjà
SET @fk_exist := (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME         = 'films'
      AND CONSTRAINT_NAME    = 'fk_films_user'
      AND CONSTRAINT_TYPE    = 'FOREIGN KEY'
);
SET @sql2 := IF(@fk_exist = 0,
    'ALTER TABLE films ADD CONSTRAINT fk_films_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
    'SELECT "FK already exists"'
);
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
