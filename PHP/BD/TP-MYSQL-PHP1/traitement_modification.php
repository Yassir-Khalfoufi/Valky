<?php
include "db.php";

$stmt = $pdo->prepare("UPDATE etudiants SET nom=?, prenom=?, date_naissance=?, adresse=?, filiere=?, niveau=? WHERE id=?");

try
{
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['date_naissance'],
        $_POST['adresse'],
        $_POST['filiere'],
        $_POST['niveau'],
        $_POST['id']
    ]);
    echo "Étudiant modifié avec succès. <a href='liste.php'>Retour à la liste</a>";
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>