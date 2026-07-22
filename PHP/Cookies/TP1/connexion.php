<?php
$host = "localhost";
$dbname = "gestionproduit_v2";
$user = "root";
$password = "";

try 
{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
