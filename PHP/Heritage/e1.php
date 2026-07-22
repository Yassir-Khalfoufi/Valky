<?php
require("etudiant.php");
$E1 = new User("Yassir");
$E1 -> Afficher();
$E1 -> Nom;
$E1 -> Nom("Ali");
#isset($E1 -> Prenom);
#unset($E1 -> Nom);
#$serial = serialize($E1);
#echo "<br>" .$serial . "<br>"
$unserial = unserialize($E1);
echo $unserial ."<br>";
$E1("cnksdvnsdpvn");
$E2 = clone($E1);
?>