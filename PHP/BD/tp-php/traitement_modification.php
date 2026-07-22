<?php
require("connexion.php");

if (
    isset($_POST["id"]) &&
    isset($_POST["nom"]) &&
    isset($_POST["prenom"]) &&
    isset($_POST["date_naissance"]) &&
    isset($_POST["adresse"]) &&
    isset($_POST["filiere"]) &&
    isset($_POST["niveau"])
) {

    $id = $_POST["id"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $date_naissance = $_POST["date_naissance"];
    $adresse = $_POST["adresse"];
    $filiere = $_POST["filiere"];
    $niveau = $_POST["niveau"];

    $update = $pdo->prepare("UPDATE etudiants
        SET nom = :nom,
            prenom = :prenom,
            date_naissance = :dn,
            adresse = :adresse,
            filiere = :filiere,
            niveau = :niveau
        WHERE id = :id");

    $update->execute([
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":dn" => $date_naissance,
        ":adresse" => $adresse,
        ":filiere" => $filiere,
        ":niveau" => $niveau,
        ":id" => $id
    ]);

     header("Location: liste.php?message=modifie");
        exit();

} else {
    echo "Tous les champs sont obligatoires !";
}
?>
