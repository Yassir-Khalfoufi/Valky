<?php require_once 'autoload.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>TP POO PHP</title></head>
<body>

<h2>Exercice 1 – Équation du 2nd degré</h2>
<form method="post">
  a: <input type="number" step="any" name="a" value="<?= $_POST['a'] ?? 1 ?>">
  b: <input type="number" step="any" name="b" value="<?= $_POST['b'] ?? -3 ?>">
  c: <input type="number" step="any" name="c" value="<?= $_POST['c'] ?? 2 ?>">
  <button type="submit" name="ex1">Résoudre</button>
</form>
<?php
if (isset($_POST['ex1'])) {
    $eq = new Eq2Degre((float)$_POST['a'], (float)$_POST['b'], (float)$_POST['c']);
    echo "<p>"; $eq->afficheDiscriminant(); echo "</p>";
    echo "<p>"; $eq->afficheSolutions(); echo "</p>";
}
?>

<hr>
<h2>Exercice 2 – Bien immobilier</h2>
<form method="post">
  Référence: <input type="text" name="ref" value="<?= $_POST['ref'] ?? 'B001' ?>">
  Type: <input type="text" name="type" value="<?= $_POST['type'] ?? 'Villa' ?>">
  Surface: <input type="number" step="any" name="surface" value="<?= $_POST['surface'] ?? 120 ?>">
  Prix: <input type="number" step="any" name="prix" value="<?= $_POST['prix'] ?? 500000 ?>">
  Ville: <input type="text" name="ville" value="<?= $_POST['ville'] ?? 'Casablanca' ?>">
  <button type="submit" name="ex2">Créer Bien</button>
</form>
<?php
if (isset($_POST['ex2'])) {
    $bien = new Bien($_POST['ref'], $_POST['type'], (float)$_POST['surface'], (float)$_POST['prix'], $_POST['ville']);
    echo "<p>"; $bien->afficher(); echo "</p>";
}
?>

<hr>
<h2>Exercice 3 – Héritage : Appartement, Immeuble, Terrain</h2>

<h3>Appartement</h3>
<?php
$appt = new Appartement('A001', 85, 750000, 'Rabat', 3, 4);
echo "<p>"; $appt->afficher(); echo "</p>";
?>

<h3>Immeuble</h3>
<?php
$imm = new Immeuble('I001', 600, 4500000, 'Casablanca', 5, 20);
echo "<p>"; $imm->afficher(); echo "</p>";
?>

<h3>Terrain</h3>
<?php
$ter = new Terrain('T001', 300, 200000, 'Marrakech', true);
echo "<p>"; $ter->afficher(); echo "</p>";
?>

</body>
</html>
