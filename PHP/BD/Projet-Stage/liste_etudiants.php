<?php
require_once 'dbconfig.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY NCE")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants</title>
</head>
<body>
<div class="container">
    <h2>Liste des étudiants</h2>
    <a href="ajouter_etudiant.php" class="btn-add">+ Ajouter un étudiant</a>

    <?php if (empty($etudiants)): ?>
        <p>Aucun étudiant enregistré.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>NCE</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Classe</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($etudiants as $etudiant): ?>
            <tr>
                <td><?= $etudiant['NCE'] ?></td>
                <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                <td><?= htmlspecialchars($etudiant['classe']) ?></td>
                <td>
                    <a href="supprimer_etudiant.php?NCE=<?= $etudiant['NCE'] ?>"
                       class="btn-delete"
                       onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                    <a href="modifier_etudiant.php?NCE=<?= $etudiant['NCE'] ?>"
                       class="btn-edit">Modifier</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <a href="index.php">← Retour à l'accueil</a>
</div>
</body>
</html>
