<?php
function triangle($larg, $long){
    $aire = $larg * $long;
    echo "Un rectangle de longeur ".$long." et de largeur ".$larg." a une aire de : ".$aire;
}
triangle(5,6);
?>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Aire du Rectangle</title>
</head>
<body>
  <h2>Calculer l'aire d'un rectangle</h2>
  <form method="POST" action="triangle.php">
    <label for="largeur">Largeur :</label>
    <input type="number" id="largeur" name="larg" required><br><br>
    <label for="longueur">Longueur :</label>
    <input type="number" id="longueur" name="long" required><br><br>
    <input type="submit" value="Calculer">
  </form>
</body>
</html>
