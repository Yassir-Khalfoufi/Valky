
CREATE DATABASE IF NOT EXISTS cinelist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinelist;

CREATE TABLE IF NOT EXISTS films (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    realisateur VARCHAR(150),
    annee       YEAR,
    genre       VARCHAR(100),
    statut      ENUM('a_voir','en_cours','vu') DEFAULT 'a_voir',
    note        TINYINT UNSIGNED CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    affiche_url VARCHAR(500),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Quelques films d'exemple
INSERT INTO films (titre, realisateur, annee, genre, statut, note, commentaire) VALUES
('Blade Runner 2049',    'Denis Villeneuve', 2017, 'Sci-Fi',   'vu',      5, 'Chef-d\'œuvre visuel absolu.'),
('Parasite',             'Bong Joon-ho',     2019, 'Thriller', 'vu',      5, 'Scénario parfait.'),
('Dune: Part Two',       'Denis Villeneuve', 2024, 'Sci-Fi',   'vu',      4, 'Épique et ambitieux.'),
('The Zone of Interest', 'Jonathan Glazer',  2023, 'Drame',    'a_voir',  NULL, NULL),
('Oppenheimer',          'Christopher Nolan',2023, 'Biopic',   'en_cours',NULL, NULL);
