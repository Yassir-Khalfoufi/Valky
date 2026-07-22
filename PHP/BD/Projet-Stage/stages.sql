-- Création de la base de données
CREATE DATABASE IF NOT EXISTS stages CHARACTER SET utf8 COLLATE utf8_general_ci;
USE stages;

-- Table Etudiant
CREATE TABLE IF NOT EXISTS Etudiant (
    NCE        INT PRIMARY KEY AUTO_INCREMENT,
    nom        VARCHAR(100) NOT NULL,
    prenom     VARCHAR(100) NOT NULL,
    classe     VARCHAR(50)  NOT NULL
);

-- Table Enseignant
CREATE TABLE IF NOT EXISTS Enseignant (
    Matricule   INT PRIMARY KEY AUTO_INCREMENT,
    nom_Ens     VARCHAR(100) NOT NULL,
    prenom_Ens  VARCHAR(100) NOT NULL
);

-- Table Soutenance
CREATE TABLE IF NOT EXISTS Soutenance (
    Numjury          INT PRIMARY KEY AUTO_INCREMENT,
    date_soutenance  VARCHAR(20)    NOT NULL,
    note             DECIMAL(5,2)   NOT NULL,
    NCE              INT            NOT NULL,
    Matricule        INT            NOT NULL,
    FOREIGN KEY (NCE)       REFERENCES Etudiant(NCE)      ON DELETE CASCADE,
    FOREIGN KEY (Matricule) REFERENCES Enseignant(Matricule) ON DELETE CASCADE
);

-- Table Administrateur
CREATE TABLE IF NOT EXISTS Administrateur (
    id_admin    INT PRIMARY KEY AUTO_INCREMENT,
    login       VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Insertion d'un administrateur par défaut (mot de passe : admin123)
INSERT INTO Administrateur (login, mot_de_passe)
VALUES ('admin', SHA2('admin123', 256));

-- Données de test
INSERT INTO Etudiant (nom, prenom, classe) VALUES
('Riahi',    'Ahmed',  'DSI31'),
('Ben Salem','Serine', 'DSI22'),
('Gharbi',   'Faten',  'RSI21'),
('Ben Amor', 'Tarek',  'TI101');

INSERT INTO Enseignant (nom_Ens, prenom_Ens) VALUES
('Tlili',    'Mohamed'),
('Maatoug',  'Sonia'),
('Ben Youssef', 'Karim');

INSERT INTO Soutenance (date_soutenance, note, NCE, Matricule) VALUES
('15/12/2019', 14.5, 1, 1),
('15/12/2019', 16.0, 2, 1),
('15/12/2019', 12.0, 3, 2),
('20/12/2019', 15.5, 4, 3);
