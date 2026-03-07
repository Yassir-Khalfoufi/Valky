<!DOCTYPE html>
<html>
<head>
    <title>Produits</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<ul>
    <li><a href="index.html">Accueil</a></li>
    <li><a href="produits.php">Produits</a></li>
    <li><a href="contact.html">Contact</a></li>
    <li><a href="feedback.html">Feedback</a></li>
</ul>
</nav>
<center>
<h2>Nos Produits</h2>

<?php
$produits = [
    ["nom" => "PC Portable", "description" => "Ordinateur performant", "prix" => 800],
    ["nom" => "Souris Gaming", "description" => "Souris RGB", "prix" => 40],
    ["nom" => "Clavier Mécanique", "description" => "Clavier rétroéclairé", "prix" => 90]
];

echo "<table border='1'>";
echo "<tr><th>Nom</th><th>Description</th><th>Prix (€)</th></tr>";

foreach ($produits as $produit) {
    echo "<tr>";
    echo "<td>".$produit["nom"]."</td>";
    echo "<td>".$produit["description"]."</td>";
    echo "<td>".$produit["prix"]."</td>";
    echo "</tr>";
}

echo "</table>";
?>
</center>
</body>
</html>
