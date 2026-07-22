<!DOCTYPE html>
<html>
<body>
<h2>Ajouter un étudiant</h2>
<form method="POST" action="traitement_ajout.php">
    Nom: <input type="text" name="nom"><br><br>
    Prénom: <input type="text" name="prenom"><br><br>
    Date de naissance: <input type="date" name="date_naissance"><br><br>
    Adresse: <input type="text" name="adresse"><br><br>
    Filière: <input type="text" name="filiere"><br><br>
    Niveau: <input type="number" name="niveau"><br><br>
    <input type="submit" value="Ajouter">
</form>
<div>
    <label>Image</label>
    <input type="file" name="image">
</div>
</body>
</html>
