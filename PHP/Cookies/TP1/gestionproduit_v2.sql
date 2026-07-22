create database if not exists gestionproduit_v2;
use gestionproduit_v2;
create table if not exists Categorie
(
    idCategorie int auto_increment primary key,
    denomination varchar(255),
    description1 varchar(255)
);
create table Produit
(
    reference int unique,
    libelle varchar(255),
    prixUnitaire float,
    dateAchat date,
    photoProduit varchar(255),
    idCategorie int,
    foreign key (idCategorie) references Categorie(idCategorie)
);
create table CompteProprietaire(
    loginProp varchar(255),
    motPasse varchar(255),
    nom varchar(255),
    prenom varchar(255)
);
