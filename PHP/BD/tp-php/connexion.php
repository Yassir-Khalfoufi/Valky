<?php

$host ="localhost";
$user = "root";
$pass="";
$dbname="etudiants";

try {
    $pdo =  new PDO("mysql:host=$host;dbname=$dbname;", $user, $pass);
} catch (PDOException $e) {
    echo "Erreur :".$e->getMessage();
}