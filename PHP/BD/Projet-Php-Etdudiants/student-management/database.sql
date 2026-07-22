CREATE DATABASE IF NOT EXISTS gestion_etudiants
USE gestion_etudiants;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'prof') NOT NULL DEFAULT 'prof',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des notes
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    matiere VARCHAR(100) NOT NULL,
    note DECIMAL(5,2) NOT NULL CHECK (note >= 0 AND note <= 20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE
);

-- Compte admin par défaut : email = admin@univ.ma / mot de passe = admin123
-- Le hash ci-dessous correspond à password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES ('Administrateur', 'admin@univ.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Données de test
INSERT INTO etudiants (nom, prenom, email, date_naissance, filiere) VALUES
('Fall', 'Lamine', 'fall.lamine@mail.com', '2000-05-15', 'Informatique'),
('Dupont', 'Marie', 'dupont.marie@mail.com', '2001-03-22', 'Mathematiques'),
('Benali', 'Youssef', 'benali.youssef@mail.com', '1999-11-08', 'Physique');

INSERT INTO notes (etudiant_id, matiere, note) VALUES
(1, 'Algorithmique', 16.5),
(1, 'Base de donnees', 14.0),
(2, 'Analyse', 18.0),
(2, 'Algebre', 15.5),
(3, 'Mecanique', 12.0),
(3, 'Thermodynamique', 13.5);
