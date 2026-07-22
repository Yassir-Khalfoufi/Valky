<?php
function Age($datens) {
    $age = date("Y") - date("Y", strtotime($datens));
    return $age;
}

$resultat = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datens = $_POST["datens"];
    $age = Age($datens);

    if ($age > 0) {
        $resultat = $age;
    }
}
?>