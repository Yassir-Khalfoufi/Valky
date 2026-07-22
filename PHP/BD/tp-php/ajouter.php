<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Ajouter un étudiant</h2>

    <form action="traitement_ajout.php" method="POST" class="card p-4 shadow">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Adresse</label>
            <input type="text" name="adresse" class="form-control">
        </div>

        <div class="mb-3">
            <label>Filière</label>
            <input type="text" name="filiere" class="form-control">
        </div>

        <div class="mb-3">
            <label>Niveau</label>
            <input type="number" name="niveau" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="liste.php" class="btn btn-secondary">Retour</a>
        l'ajout des imge au niveau de database 
        <div class="mb-3">
           <label for="image">image</label>
        <input type="file" name="image" class="form-control" accept="image/*">

        <button type="submit">Uploader l'image</button>
    </div>
    </form>
</div>

</body>
</html>
