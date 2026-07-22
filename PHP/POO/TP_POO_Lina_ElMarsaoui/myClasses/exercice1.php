<?php
require_once "autoload.php";

if (isset($_POST['a'], $_POST['b'], $_POST['c'])) {
    $eq = new Eq2Degre($_POST['a'], $_POST['b'], $_POST['c']);
    $eq->afficheDiscriminant();
    $eq->afficheSolutions();
}
?>

<form method="post">
a: <input type="number" name="a"><br>
b: <input type="number" name="b"><br>
c: <input type="number" name="c"><br>
<button type="submit">OK</button>
</form>