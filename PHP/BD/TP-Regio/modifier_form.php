<?php
require 'connexion.php';

$id = $_GET['id'];

$stmt = $pdo -> prepare("SELECT * FROM joueurs WHERE id = :id");
$stmt -> execute([':id' => $id]);
$joueur = $stmt -> fetch(PDO::FETCH_ASSOC);
?>

<html>
<body>
    <h2>Modifier un joueur</h2>
    <form method="POST" action="modifier.php">
        <input type="hidden" name="id" value="<?= $joueur['id'] ?>">

        <label>Nom du Joueur :</label><br>
        <input type="text" name="nom" value="<?= ($joueur['nom']) ?>"><br><br>

        <label>Prénom du Joueur :</label><br>
        <input type="text" name="prenom" value="<?= ($joueur['prenom']) ?>"><br><br>

        <label>Age du Joueur :</label><br>
        <input type="number" name="age" value="<?= ($joueur['age']) ?>"><br><br>

        <input type="submit" value="Modifier">
    </form>
</body>
</html>
