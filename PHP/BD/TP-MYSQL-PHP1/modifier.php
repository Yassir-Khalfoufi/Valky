<?php
include "db.php";
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$_GET['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<body>
<h2>Modifier un étudiant</h2>
<form method="POST" action="traitement_modification.php">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    Nom: <input type="text" name="nom" value="<?= $row['nom'] ?>"><br><br>
    Prénom: <input type="text" name="prenom" value="<?= $row['prenom'] ?>"><br><br>
    Date de naissance: <input type="date" name="date_naissance" value="<?= $row['date_naissance'] ?>"><br><br>
    Adresse: <input type="text" name="adresse" value="<?= $row['adresse'] ?>"><br><br>
    Filière: <input type="text" name="filiere" value="<?= $row['filiere'] ?>"><br><br>
    Niveau: <input type="number" name="niveau" value="<?= $row['niveau'] ?>"><br><br>
    <input type="submit" value="Modifier">
</form>
</body>
</html>
