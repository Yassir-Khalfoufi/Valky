<?php
require_once 'dbconfig.php';

$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom    = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $classe = trim($_POST['classe']);

        if ($nom && $prenom && $classe) {
            $stmt = $pdo->prepare("INSERT INTO Etudiant (nom, prenom, classe) VALUES (:nom, :prenom, :classe)");
            $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':classe' => $classe]);
            $message = "<p class='success'> Étudiant ajouté avec succès !</p>";
        } else {
            $message = "<p class='error'> Veuillez remplir tous les champs.</p>";
        }
    }
} catch (PDOException $e) {
    $message = "<p class='error'>Erreur : " . $e->getMessage() . "</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un étudiant</title>
</head>
<body>
<div class="container">
    <h2>Ajouter un étudiant</h2>
    <?= $message ?>
    <form method="POST" action="ajouter_etudiant.php">
        <label>Nom :</label>
        <input type="text" name="nom" required>

        <label>Prénom :</label>
        <input type="text" name="prenom" required>

        <label>Classe :</label>
        <input type="text" name="classe" required>

        <button type="submit">Ajouter</button>
    </form>
    <a href="liste_etudiants.php">← Voir la liste des étudiants</a>
</div>
</body>
</html>
