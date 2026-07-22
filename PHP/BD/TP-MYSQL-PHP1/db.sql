create database if not exists etudiants;
use etudiants;
create table if not exists etudiants (
    id int auto_increment primary key,
    nom varchar(255),
    prenom varchar(255),
    date_naissance date,
    adresse varchar(255),
    filiere varchar(255),
    niveau int
);
ALTER TABLE etudiants ADD COLUMN photo VARCHAR(255);
