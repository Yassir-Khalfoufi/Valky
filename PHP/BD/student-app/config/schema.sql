-- base de données
create database if not exists gestion_etudiants;
use gestion_etudiants;

-- table utilisateurs
create table if not exists utilisateurs (
    id            int unsigned auto_increment primary key,
    nom           varchar(100) not null,
    email         varchar(150) not null unique,
    mot_de_passe  varchar(255) not null,
    role          enum('admin', 'prof') not null default 'prof',
    created_at    timestamp default current_timestamp
) engine=innodb;

-- table etudiants
create table if not exists etudiants (
    id               int unsigned auto_increment primary key,
    nom              varchar(100) not null,
    prenom           varchar(100) not null,
    email            varchar(150) not null unique,
    date_naissance   date not null,
    filiere          varchar(100) not null,
    created_at       timestamp default current_timestamp
) engine=innodb;

-- table notes
create table if not exists notes (
    id           int unsigned auto_increment primary key,
    etudiant_id  int unsigned not null,
    matiere      varchar(100) not null,
    note         decimal(4,2) not null check (note >= 0 and note <= 20),
    foreign key (etudiant_id) references etudiants(id) on delete cascade
) engine=innodb;

-- admin par défaut : mot de passe = admin123
insert into utilisateurs (nom, email, mot_de_passe, role)
values ('Administrateur', 'admin@univ.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
