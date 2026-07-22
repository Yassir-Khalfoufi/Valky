CREATE DATABASE IF NOT EXISTS VolAvion;
USE VolAvion;

SET FOREIGN_KEY_CHECKS = 0;

-- ===============================
-- TABLE AVION
-- ===============================
CREATE TABLE avion (
    NumAvion SMALLINT NOT NULL,
    Marque VARCHAR(10) NOT NULL,
    TypeAvion VARCHAR(10) NOT NULL,
    Capacite SMALLINT NOT NULL,
    localisation VARCHAR(25) NOT NULL,
    DateMiseEnService DATETIME NULL,
    PRIMARY KEY (NumAvion)
) ENGINE=InnoDB;

-- ===============================
-- TABLE PASSAGER
-- ===============================
CREATE TABLE Passager (
    Numpass INT NOT NULL,
    nom VARCHAR(20) NOT NULL,
    prenom VARCHAR(20) NOT NULL,
    ville VARCHAR(25) NOT NULL,
    PRIMARY KEY (Numpass)
) ENGINE=InnoDB;

-- ===============================
-- TABLE PILOTE
-- ===============================
CREATE TABLE pilote (
    Numpil SMALLINT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(10) NOT NULL,
    CodePostal CHAR(5) NOT NULL,
    Ville CHAR(26) NOT NULL,
    DateNaissance DATETIME NULL,
    DateDebutActivite DATETIME NULL,
    DateFinActivite DATETIME NULL,
    SalaireBrut DECIMAL(15,4) DEFAULT 1500,
    PRIMARY KEY (Numpil)
) ENGINE=InnoDB;

-- ===============================
-- TABLE VOL
-- ===============================
USE VolAvion;
CREATE TABLE vol (
    Numvol SMALLINT NOT NULL,
    avion SMALLINT NOT NULL,
    pilote SMALLINT NOT NULL,
    villedepart VARCHAR(26) NOT NULL,
    villearrivee VARCHAR(26) NOT NULL,
    heuredepart DECIMAL(5,2) NOT NULL,
    heurearrivee DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (Numvol),
    CONSTRAINT FK_vol_avion FOREIGN KEY (avion)
        REFERENCES avion(NumAvion)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT FK_vol_pilote FOREIGN KEY (pilote)
        REFERENCES pilote(Numpil)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===============================
-- TABLE AFFECTEVOL
-- ===============================
USE VolAvion;
CREATE TABLE AffecteVol (
    passager INT NOT NULL,
    vol SMALLINT NOT NULL,
    datevol DATETIME NOT NULL,
    numplace SMALLINT NOT NULL,
    prix DECIMAL(15,4) NULL,
    CONSTRAINT FK_AffecteVol_Passager FOREIGN KEY (passager)
        REFERENCES Passager(Numpass)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FK_AffecteVol_vol FOREIGN KEY (vol)
        REFERENCES vol(Numvol)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================================
-- INSERTIONS DES DONNÉES
-- ===================================
USE VolAvion;
INSERT INTO avion VALUES
(100,'AIRBUS','A320',381,'Nice','1977-03-20'),
(101,'Boeing','B707',250,'Paris','1985-02-27'),
(102,'AIRBUS','A320',522,'Toulouse','1988-01-24'),
(103,'Caravelle','Caravelle',240,'Toulouse','1964-01-01'),
(104,'Boeing','B747',400,'Paris','1968-01-01'),
(105,'AIRBUS','A320',423,'Grenoble','1968-05-01'),
(106,'Atr','ATR42',500,'Paris','1950-01-01'),
(107,'Boeing','B727',300,'Lyon','1988-01-01'),
(108,'Boeing','B727',300,'Nantes','1988-01-01'),
(109,'AIRBUS','A340',350,'Bastia','1995-01-01'),
(120,'Caravelle','Caravelle',240,'Grenoble','1960-01-01'),
(150,'AIRBUS','A340',345,'Brive','2000-01-01'),
(151,'Boeing','B707',250,'Bastia','1976-02-02'),
(155,'AIRBUS','A340',600,'Toulouse','1998-06-03'),
(160,'AIRBUS','A340',600,'Paris','1978-02-06'),
(170,'AIRBUS','A340',600,'bruxelles','1978-02-06');
USE VolAvion;
INSERT INTO Passager VALUES
(1,'MAUSSE','Fabien','Toulouse'),
(2,'MERLHIOT','Pascal','Paris'),
(3,'JEAN','Patrick','Nice'),
(4,'PEREIRA','Joao','Limoge'),
(5,'FREEMAN','Cathy','Paris'),
(6,'MINETTE','Sophie','Grenoble'),
(7,'BARTEAM','Fred','Lyon'),
(8,'GLLOQ','Gille','Fort de France'),
(9,'BOST','Vincent','Brive');
USE VolAvion;
INSERT INTO pilote (Numpil,nom,CodePostal,Ville,DateNaissance,DateDebutActivite,DateFinActivite,SalaireBrut) VALUES
(1,'Serge','31000','tours','1954-08-04','1980-01-01',NULL,15117.6618),
(2,'Jean','75010','Paris','1954-05-25','1978-02-01',NULL,10000),
(3,'Roger','38000','Grenoble','1954-05-25','1990-04-01',NULL,20156.3853),
(4,'Robert','44000','Nantes','1953-12-20','1993-06-01',NULL,10000),
(5,'Michel','75010','Paris','1954-05-25','2000-01-01',NULL,10000),
(7,'Bertrand','69001','Lyon','1953-05-25','1988-01-01',NULL,10000),
(8,'Hervé','20000','Moscou','1954-05-25','1987-01-01',NULL,10000),
(9,'Luc','75018','Paris','1954-05-25','1985-01-01',NULL,10000),
(19,'Driss','75006','Paris','1954-05-25','1990-12-02',NULL,10000),
(20,'Sylvain','31250','Gardignac','1954-05-25','2000-01-01',NULL,10000),
(21,'Lucien','31250','Gardignac','1954-05-25','1995-10-03',NULL,10000),
(29,'Laverdure','33000','Toulouse',NULL,'2003-12-30',NULL,8784.6),
(30,'Bost','19100','brive',NULL,NULL,NULL,1500),
(31,'Bost','19100','brive',NULL,NULL,NULL,1500);
USE VolAvion;
INSERT INTO vol VALUES
(100,100,1,'Nice','Paris',7.20,9.00),
(101,100,2,'Paris','Toulouse',11.20,12.00),
(102,101,1,'Paris','Nice',12.35,14.00),
(103,105,3,'Grenoble','Toulouse',9.00,11.00),
(104,105,3,'Toulouse','Grenoble',17.00,19.00),
(105,107,7,'Lyon','Paris',6.00,7.00),
(106,109,8,'Bastia','Paris',10.00,13.00),
(107,106,9,'Paris','Brive',7.00,8.00),
(108,106,9,'Brive','Paris',19.00,20.00),
(109,107,7,'Touloujjfeujheuehuehe','Grenoble',18.00,19.00),
(110,102,2,'Toulouse','Paris',15.00,16.00),
(111,108,5,'NICE','Paris',14.00,16.00),
(112,109,2,'Bastia','Paris',10.00,13.00),
(113,105,2,'Toulouse','Grenoble',17.00,19.00),
(114,150,2,'Paris','Marseille',10.00,12.00),
(115,155,2,'Paris','Lille',11.00,12.00),
(116,101,4,'Nice','Nantes',17.00,19.00),
(747,104,1,'Moulinsart','Sydney',1.00,23.00);

INSERT INTO AffecteVol VALUES
(6,100,'2000-11-01',54,1200),
(5,100,'2000-11-01',55,1200),
(7,100,'2000-11-01',66,1200),
(1,103,'2000-11-05',12,900),
(3,103,'2000-11-05',23,900);

SET FOREIGN_KEY_CHECKS = 1;

