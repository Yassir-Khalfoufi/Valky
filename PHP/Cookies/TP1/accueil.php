<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
require 'connexion.php';

// a. Bonjour / Bonsoir
$heure = date('H');
$salutation = ($heure >= 6 && $heure < 18) ? "Bonjour" : "Bonsoir";

// b. Liste des produits triés par libelle
$stmt = $conn->query("
    SELECT p.*, c.denomination 
    FROM Produit p 
    LEFT JOIN Categorie c ON p.idCategorie = c.idCategorie 
    ORDER BY p.libelle
");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<body>
<nav>
    <a href="accueil.php">Accueil</a>
    <a href="ajouterProduit.php">Ajouter Produits</a>
    <a href="quitter.php">Quitter la session</a>
</nav>

<h2><?= $salutation . " " . $_SESSION['nom'] . " " . $_SESSION['prenom']; ?></h2>

<h3>Produits</h3>
<table border="1">
    <tr>
        <th>Reference</th>
        <th>Libelle</th>
        <th>Prix Unitaire</th>
        <th>Date Achat</th>
        <th>Photo Produit</th>
        <th>Categorie</th>
        <th>ACTION</th>
    </tr>
    <?php foreach ($produits as $p): ?>
    <tr>
        <td><?= $p['reference'] ?></td>
        <td><?= $p['libelle'] ?></td>
        <td><?= $p['prixUnitaire'] ?></td>
        <td><?= $p['dateAchat'] ?></td>
        <td>
            <?php if ($p['photoProduit']): ?>
                <img src="images/<?= $p['photoProduit'] ?>" width="50">
            <?php endif; ?>
        </td>
        <td><?= $p['denomination'] ?></td>
        <td>
            <a href="modifierProduit.php?ref=<?= $p['reference'] ?>">✏️</a>
            <a href="supprimerProduit.php?ref=<?= $p['reference'] ?>" onclick="return confirm('Confirmer la suppression ?')">🗑️</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>