<?php
require("connexion.php");
if(isset($_POST["nom"]) && isset($_POST["prenom"])
    && isset($_POST["date_naissance"])&&
    isset($_POST["adresse"])&& isset($_POST["filiere"])
        && isset($_POST["niveau"])){

        $Nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $date_naissance = $_POST["date_naissance"];
        $adresse = $_POST["adresse"];
        $filiere = $_POST["filiere"];
        $niveau = $_POST["niveau"];

        $imageName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        $chemin = "uploads/".$imageName;

        move_upload_file($tmpName, $chemin);

    $insert = $pdo ->prepare("INSERT into 
            etudiants(nom,prenom,date_naissance,adresse,filiere,niveau,image)
            values (:n, :p, :dn, :a, :f, :n, :image)");
    $insert->execute([
        ":n"=>$Nom, ":p"=>$prenom, ":dn"=>$date_naissance,
        ":a"=>$adresse, ":f"=>$filiere, ":n"=>$niveau
    ]);
    echo "insertion réussie";
}