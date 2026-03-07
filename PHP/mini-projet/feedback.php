<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = !empty($_POST["nom"]) ? $_POST["nom"] : "Anonyme";
    $note = $_POST["note"];
    $avis = $_POST["avis"];

    if (empty($avis)) {
        echo "L'avis ne peut pas être vide.";
    } else {
        echo "<div style='border:1px solid #000; padding:15px; margin:15px;'>";
        echo "<strong>$nom</strong><br>";
        echo "Note : $note / 5<br><br>";
        echo "$avis";
        echo "</div>";
    }
}
?>
