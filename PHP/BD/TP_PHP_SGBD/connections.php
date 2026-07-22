<?php
$host = "localhost";
$user = "root";
$pass = "mlkmlkasdasd";
$dbname = "airbnb";
try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;",$user,$pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	echo "connection reussie";
}
catch (PDOException $e) {
	echo "Erreur :".$e->getmessage();
}
?>
