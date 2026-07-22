<?php
#import de class
require("administrateur.php");
require("user.php")
#instansation (creattion des objets)
$Ad1 = new Administrateur("Ali","MSI");
#echo $Ad1 ; #appel methode tostring
#appel de methode de classe 
#Administrateur::Afficher();
$U1 = new Utilisateur("klb","MSH","Info");
echo $U1;
?>