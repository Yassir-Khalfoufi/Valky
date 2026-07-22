<?php
require_once 'dbconfig.php';

$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom_Ens    = trim($_POST['nom_Ens']);
        $prenom_Ens = trim($_POST['prenom_Ens']);

        if ($nom_Ens && $prenom_Ens) {
            $stmt = $pdo->prepare("INSERT INTO Enseignant (nom_Ens, prenom_Ens) VALUES (:nom_Ens, :prenom_Ens)");
            $stmt->execute([':nom_Ens' => $nom_Ens, ':prenom_Ens' => $prenom_Ens]);
            $message = "<p class='success'>✔ Enseignant ajouté avec succès !</p>";
        } else {
            $message = "<p class='error'>⚠ Veuillez remplir tous les champs.</p>";
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
    <title>Ajouter un enseignant</title>
</head>
<body>
<div class="container">
    <h2>Ajouter un enseignant</h2>
    <?= $message ?>
    <form method="POST" action="ajouter_enseignant.php">
        <label>Nom :</label>
        <input type="text" name="nom_Ens" required>

        <label>Prénom :</label>
        <input type="text" name="prenom_Ens" required>

        <button type="submit">Ajouter</button>
    </form>
    <a href="index.php">← Retour à l'accueil</a>
</div>
</body>
</html>
