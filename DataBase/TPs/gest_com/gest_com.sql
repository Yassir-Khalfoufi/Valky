create database gest_com;
use gest_com;
create table Produits(
	Num_Produit varchar(18) primary key,
    description varchar(40),
    cout decimal(10,2) not null check(cout > 0),
    prix decimal(10,2) not null check(prix > 0),
    date_dajout date,
    constraint cp check(cout > prix)
);
insert into Produits values('p2', 'machine a laver', 3000.00, 2500.00, 12/12/2020);
select * from Produits;

