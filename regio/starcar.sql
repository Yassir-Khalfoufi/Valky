create database starcar;
use starcar;
create table Voitures
(
    immmatriculation varchar(15), 
    marque varchar(15),
    modele varchar(20),
    annee int,
    prixjour decimal(10, 2),
    photo varchar(50)
);
create table Locations
(
    id integer ,
    user varchar(20),
    client varchar(23),
    datadebut datetime,
    nombre_jour integer,
    datefin date 
);
create table Clients
(
    id int,
    nom varchar(40),
    prenom varchar(15) ,
    email  varchar(20),
    tel varchar(15)
)
create table Users
(
    nomUtilisateur varchar(55),
    passwordq varchar(255),
    roleq varchar(255),
)