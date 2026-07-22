<?php
require("connections.php");

if (isset($_POST["nom"]) && isset($_POST["email"]) && isset($_POST["password"])) {

    $Nom = $_POST["nom"];
    $Email = $_POST["email"];
    $Pass = $_POST["password"];
    
    $hashPassword = password_hash($Pass, 
    PASSWORD_DEFAULT);

    $stm = $pdo->prepare("INSERT INTO user (Nom, Email, password) 
                          VALUES (:Nom, :Email, :Pass)");

    $stm->execute([
        ":Nom" => $Nom,
        ":Email" => $Email,
        ":Pass" => $hashPassword
    ]);

    echo "Inscription réussie";
}
?>