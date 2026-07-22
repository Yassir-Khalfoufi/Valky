<?php
require_once "autoload.php";

if (isset($_POST['id'], $_POST['type'], $_POST['prix'])) {
    $b = new Bien($_POST['id'], $_POST['type'], $_POST['prix']);
    $b->afficher();
}
?>

<form method="post">
id: <input type="number" name="id"><br>
type: <input type="text" name="type"><br>
prix: <input type="number" name="prix"><br>
<button type="submit">OK</button>
</form>