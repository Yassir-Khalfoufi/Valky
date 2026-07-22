<?php
require("connexion.php");

$data = $pdo->prepare("SELECT * from etudiants");
$data->execute();
$liste = $data->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if (isset($_GET['message']) && $_GET['message'] == 'modifie'): ?>
    <div class="alert alert-success">
        Étudiant modifié avec succès !
    </div>
<?php endif; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des étudiants</h2>

    <a href="ajouter.php" class="btn btn-success mb-3">Ajouter un étudiant</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>Adresse</th>
                <th>Filière</th>
                <th>Niveau</th>
                <th>Actions</th>
            </tr>

            <?php foreach($liste as $etu): ?>
            <tr>
                <td><?php echo $etu["id"];?></td>
                <td><?php echo $etu["nom"];?></td>
                <td><?php echo $etu["prenom"];?></td>
                <td><?php echo $etu["date_naissance"];?></td>
                <td><?php echo $etu["adresse"];?></td>
                <td><?php echo $etu["filiere"];?></td>
                <td><?php echo $etu["niveau"];?></td>
                <td>
                    <a href="modifier.php?id=<?= $etu['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="supprimer.php?id=<?= $etu['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach;?>
        </thead>
        <tbody>

            

        </tbody>
    </table>
</div>

</body>
</html>
