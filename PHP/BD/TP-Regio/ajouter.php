<?php
require 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $age    = $_POST['age'];

    $stmt = $pdo->prepare("INSERT INTO joueurs (nom, prenom, age) VALUES (:nom, :prenom, :age)");
    $stmt->execute([
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':age'    => $age
    ]);

    header("Location: liste.php");
    exit;
}
?>
<html>
<body>
    <h2>Ajouter un joueur</h2>
    <form method="POST" action="ajouter.php">
        <label>Nom du Joueur :</label><br>
        <input type="text" name="nom"><br><br>

        <label>Prénom du Joueur :</label><br>
        <input type="text" name="prenom"><br><br>

        <label>Age du Joueur :</label><br>
        <input type="number" name="age"><br><br>

        <input type="submit" value="Ajouter">
    </form>
</body>
</html>
