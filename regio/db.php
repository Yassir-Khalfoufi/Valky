<?php
$user = "admin" ;
$host = "172.16.0.99";
$dbname = "starcar";
$pass = "$124St@rCar+";

try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;",$user,$pass);
	$pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	echo "Connection reussie";
}
catch (PDOException $e) {
	echo "Erreur :".$e->getmessage();
}
?>
