USE VolAvion;
-- select * from vol
-- where heuredepart > 16
-- and heurearrivee < 20
-- and villedepart like 'toulou%'
select distinct Marque, TypeAvion, Capacite from avion
where Capacite > 250
order by Capacite asc