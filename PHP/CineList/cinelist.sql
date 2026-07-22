-- ============================================================
--  CineList — SQL complet (base + auth + structure)
--  Exécuter une seule fois dans phpMyAdmin ou mysql CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS cinelist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinelist;

-- ── Table utilisateurs ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Table films ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS films (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT DEFAULT NULL,
    titre        VARCHAR(200) NOT NULL,
    realisateur  VARCHAR(150),
    annee        YEAR,
    genre        VARCHAR(100),
    statut       ENUM('a_voir','en_cours','vu') DEFAULT 'a_voir',
    note         TINYINT UNSIGNED CHECK (note BETWEEN 1 AND 5),
    commentaire  TEXT,
    affiche      LONGBLOB,
    affiche_mime VARCHAR(50),
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Table tokens Remember Me ─────────────────────────────────
CREATE TABLE IF NOT EXISTS remember_tokens (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT         NOT NULL,
    token      VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME    NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Compte démo : admin / admin123 ───────────────────────────
-- hash bcrypt de "admin123" (cost=12) — CHANGE-LE après le premier login
INSERT IGNORE INTO users (username, email, password) VALUES
('admin', 'admin@cinelist.local',
 '$2y$12$KURO5VB3e7yFw5KoiNlEkuWzmx3jlJyr8BxFLvivIxg2fq5XJqxAm');
