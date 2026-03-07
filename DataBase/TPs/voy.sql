create database avion;
use avion;
create table avions(
	No_AV int primary key,
    NOM_AV varchar(20),
    CAP smallint,
    LOC varchar(15)
	);
create table pilotes(
	no_PIL int primary key,
    NOM_PIL varchar(20),
    ville varchar(15)
    );
create table vols(
	no_VOL varchar(5),
    V_d varchar(15),
    V_a varchar(15),
    H_d datetime,
    H_a datetime
    ); 
insert into pilotes values(104,'ALAMI','casa');
select * from pilotes;
