<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "cinema_db";
try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;",$user,$pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	echo "Connection reussie";
}
catch (PDOException $e) {
	echo "Erreur :".$e->getmessage();
}
?>