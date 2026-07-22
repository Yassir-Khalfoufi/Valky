<?php
require_once 'dbconfig.php';

$message = '';

if (!isset($_GET['NCE']) || !is_numeric($_GET['NCE'])) {
    header("Location: liste_etudiants.php");
    exit;
}

$NCE = intval($_GET['NCE']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom    = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $classe = trim($_POST['classe']);

        if ($nom && $prenom && $classe) {
            $stmt = $pdo->prepare("UPDATE Etudiant SET nom = :nom, prenom = :prenom, classe = :classe WHERE NCE = :NCE");
            $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':classe' => $classe, ':NCE' => $NCE]);
            $message = "<p class='success'>✔ Étudiant modifié avec succès !</p>";
        } else {
            $message = "<p class='error'>⚠ Veuillez remplir tous les champs.</p>";
        }
    }

    // Récupérer les données actuelles de l'étudiant
    $stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE NCE = :NCE");
    $stmt->execute([':NCE' => $NCE]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        die("Étudiant introuvable.");
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un étudiant</title>
</head>
<body>
<div class="container">
    <h2>Modifier l'étudiant #<?= $NCE ?></h2>
    <?= $message ?>
    <form method="POST" action="modifier_etudiant.php?NCE=<?= $NCE ?>">

        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" required>

        <label>Prénom :</label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>

        <label>Classe :</label>
        <input type="text" name="classe" value="<?= htmlspecialchars($etudiant['classe']) ?>" required>

        <button type="submit">Enregistrer les modifications</button>
    </form>
    <a href="liste_etudiants.php">← Retour à la liste</a>
</div>
</body>
</html>
