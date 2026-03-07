CREATE TABLE clients (
 id_client INT AUTO_INCREMENT PRIMARY KEY,
 nom VARCHAR(50),
 prenom VARCHAR(50),
 email varchar(59),
 telephone varchar(15)
);
alter table clients
add column age int,
add column email varchar(50),
add column telephone varchar(15);

alter table clients
modify column age int not null,
rename column telephone to tel;

alter table clients
drop column email;

alter table clients
add constraint chk_age check (age >= 18),
add column salaire decimal(10,2),
add constraint chk_salaire check (salaire > 0),
add column etat varchar(10),
add constraint chk_etat check (etat IN('acrif','inactif'));