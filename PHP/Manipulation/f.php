<!DOCTYPE html>
<html>
<head>
    <title>Enregistrement</title>
</head>
<body>

<h2>Formulaire</h2>

<form method="post">
    Nom : <input type="text" name="nom" required><br><br>
    Prénom : <input type="text" name="prenom" required><br><br>
    <input type="submit" value="Enregistrer">
</form>

<?php
if(isset($_POST["nom"]) && isset($_POST["prenom"])){
    $Nom = $_POST["nom"];
    $Prenom = $_POST["prenom"];
    
    $fichier = fopen("formulaire.txt","w");
    fwrite($fichier, $Nom." ".$Prenom."\n");
    fclose($fichier);
    echo "Données enregistrées avec succès.";
}


?>

</body>
</html>