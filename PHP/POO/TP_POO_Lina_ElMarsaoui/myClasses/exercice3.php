<?php
require_once "autoload.php";

if (isset($_POST['type'], $_POST['id'], $_POST['prix'])) {

    if ($_POST['type'] == "appartement") {
        $o = new Appartement($_POST['id'], $_POST['prix']);
    } elseif ($_POST['type'] == "immeuble") {
        $o = new Immeuble($_POST['id'], $_POST['prix']);
    } else {
        $o = new Terrain($_POST['id'], $_POST['prix']);
    }

    $o->afficher();
}
?>

<form method="post">
type:
<select name="type">
<option value="appartement">appartement</option>
<option value="immeuble">immeuble</option>
<option value="terrain">terrain</option>
</select><br>

id: <input type="number" name="id"><br>
prix: <input type="number" name="prix"><br>

<button type="submit">OK</button>
</form>