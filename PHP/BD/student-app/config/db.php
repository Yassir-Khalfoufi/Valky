<?php
$host = "localhost";
$dbname = "DB_RSK";
$user = "root";
$password = "Password_Admin";

try
    {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch (PDOException $e) 
    {
        echo"Erreur de connexion : " . $e -> getMessage();
    }
    
?>