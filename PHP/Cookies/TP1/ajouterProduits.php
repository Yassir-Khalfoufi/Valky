<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
require 'connexion.php';

// a. récupérer les catégories
$categories = $conn->query("SELECT * FROM Categorie")->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<body>
<h2>Ajouter Produit</h2>
<form method="POST" action="traitement_ajouterProduit.php" enctype="multipart/form-data">
    Libelle: <input type="text" name="libelle"><br>
    Prix Unitaire: <input type="number" name="prixUnitaire" step="0.01"><br>
    Date Achat: <input type="date" name="dateAchat"><br>
    Photo Produit: <input type="file" name="photoProduit"><br>
    Categorie:
    <select name="idCategorie">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['idCategorie'] ?>"><?= $cat['denomination'] ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Ajouter</button>
</form>
</body>
</html>