<?php require("ajouter.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Affichage des joueurs :</h1>
    <table>
        <tr>
            <th>Id</th>
            <th>Nom</th>
            <th>Prenom</th>
            <th>Adresse</th>
            <th>Filiere</th>
            <th>Date</th>
            <th>Niveau</th>
            <th>Action</th>
        </tr>
        <?php foreach($liste as $data): ?>
        <tr>
            <td><?php echo $data["id"]; ?></td>
            <td><?php echo $data["nom"]; ?></td>
            <td><?php echo $data["prenom"]; ?></td>
            <td><?php echo $data["adresse"]; ?></td>
            <td><?php echo $data["filiere"]; ?></td>
            <td><?php echo $data["date"]; ?></td>
            <td><?php echo $data["niveau"]; ?></td>
            <td><a href="modifie.php?id=<?= $data['id'] ?>">Modifier</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>