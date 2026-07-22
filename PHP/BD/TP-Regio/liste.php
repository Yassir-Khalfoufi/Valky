<?php
require 'connexion.php';

$stmt = $pdo->query("SELECT * FROM joueurs");
$joueurs = $stmt->fetchAll();
?>
<html>
<body>
    <h2>La liste des joueurs</h2>
    <a href="ajouter.php">Ajouter Joueur</a>
    <br><br>
    <table border="1">
        <tr>
            <th>Nom du Joueur</th>
            <th>Prénom du Joueur</th>
            <th>Age du Joueur</th>
            <th>Opérations</th>
        </tr>
        <?php foreach ($joueurs as $joueur): ?>
        <tr>
            <td><?= ($joueur['nom']) ?></td>
            <td><?= ($joueur['prenom']) ?></td>
            <td><?= ($joueur['age']) ?></td>
            <td>
                <a href="modifier_form.php?id=<?= $joueur['id'] ?>">Modifier</a>
                <br>
                <a href="supprimer.php?id=<?= $joueur['id'] ?>">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
