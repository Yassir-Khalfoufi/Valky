<?php
include "db.php";

$stmt = $pdo->prepare("INSERT INTO etudiants (nom, prenom, date_naissance, adresse, filiere, niveau) VALUES (?, ?, ?, ?, ?, ?)");
$imageName= $_FILES['image']['name'];
$tmpName = $_FILES['image']['tmp_name'];
$chemin = "uploads/" . $imageName;
move_uploaded_file($tmpName, $chemin);

try
{
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['date_naissance'],
        $_POST['adresse'],
        $_POST['filiere'],
        $_POST['niveau']
    ]);
    echo "Étudiant ajouté avec succès. <a href='liste.php'>Retour à la liste</a>";
}
catch (PDOException $e) 
{
    echo "Erreur: " . $e->getMessage();
}
?>
