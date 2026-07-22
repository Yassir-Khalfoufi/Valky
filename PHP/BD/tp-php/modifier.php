<?php
require("connexion.php");
$id = $_GET["id"];

$stm = $pdo->prepare("SELECT * from etudiants
        where id =:id");
$stm-> execute([":id"=>$id]);
$data = $stm->fetch();

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Modifier un étudiant</h2>

    <form action="traitement_modification.php" method="POST" class="card p-4 shadow">

        <input type="hidden" name="id" value="<?php echo $data["id"];?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" value="<?php echo $data["nom"];?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?php echo $data["prenom"];?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" value="<?php echo $data["date_naissance"];?>">
        </div>

        <div class="mb-3">
            <label>Adresse</label>
            <input type="text" name="adresse" class="form-control" value="<?php echo $data["adresse"];?>">
        </div>

        <div class="mb-3">
            <label>Filière</label>
            <input type="text" name="filiere" class="form-control" value="<?php echo $data["filiere"];?>">
        </div>

        <div class="mb-3">
            <label>Niveau</label>
            <input type="number" name="niveau" class="form-control" value="<?php echo $data["niveau"];?>">
        </div>

        <button type="submit" class="btn btn-warning">Modifier</button>
        <a href="liste.php" class="btn btn-secondary">Retour</a>

    </form>
</div>

</body>
</html>
