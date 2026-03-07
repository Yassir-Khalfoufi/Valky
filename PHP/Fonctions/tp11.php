<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Aire du Rectangle</title>
</head>
<body>

  <h2>Calculer l'aire d'un rectangle</h2>

  <form method="POST" action="">
    <label for="largeur">Largeur :</label>
    <input type="number" id="largeur" name="larg" required><br><br>

    <label for="longueur">Longueur :</label>
    <input type="number" id="longueur" name="long" required><br><br>

    <input type="submit" value="Calculer">
  </form>

</body>
</html>
<?php
function triangle($larg, $long){
    $aire = $larg * $long;
    echo "Un triangle de longueur " . $long . " et de largeur " . $larg . " a une aire de : " . $aire;
}
$resultat = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $larg = $_POST["larg"];
    $long = $_POST["long"];
}
    if ($long > 0 && $larg > 0){
        $resultat = triangle($lang, $larg);
    }
    else{
        $resultat= "Veuillez saisir des valeurs valides";
    }
?>