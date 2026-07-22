<?php
include "db.php";
$stmt = $pdo->query("SELECT * FROM etudiants");
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<body>
<h2>Liste des étudiants</h2>
<a href="ajouter.php">Ajouter un étudiant</a>
<br><br>
<table border="1">
    <tr>
        <th>ID</th><th>Nom</th><th>Prénom</th><th>Date naissance</th><th>Adresse</th><th>Filière</th><th>Niveau</th><th>Actions</th>
    </tr>
    <?php foreach ($etudiants as $row): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nom'] ?></td>
        <td><?= $row['prenom'] ?></td>
        <td><?= $row['date_naissance'] ?></td>
        <td><?= $row['adresse'] ?></td>
        <td><?= $row['filiere'] ?></td>
        <td><?= $row['niveau'] ?></td>
        <td>
            <a href="modifier.php?id=<?= $row['id'] ?>">Modifier</a> |
            <a href="supprimer.php?id=<?= $row['id'] ?>">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
