<?php
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $_POST['id'];
    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age    = $_POST['age'];

    $stmt = $pdo->prepare("UPDATE joueurs SET nom = :nom, prenom = :prenom, age = :age WHERE id = :id");
    $stmt -> execute([
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':age'    => $age,
        ':id'     => $id
    ]);
    header("Location: liste.php");
    exit;
}
?>
