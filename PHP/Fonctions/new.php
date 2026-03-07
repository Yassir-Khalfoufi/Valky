<?php
echo '-------------Annes-------------<br>';
echo "Anne en deux chiffres ".date("y")."<br>"; //return abbreviation of the year (26)
echo "Anne en quatre chiffres ".date("Y")."<br>"; //return the whole date(2026)
echo "-------------Mois-------------<br>";
echo "Mois en deux chiffres ".date("m")."<br>"; // month in 2 digits
echo "Mois en 1 chiffre ".date("n")."<br>"; //month in1  digit
echo "Mois en 3 lettres ".date("M")."<br>"; //month in 3 letters
echo "Mois en toute les lettres ".date("F")."<br>"; //whole month
echo "Nombre du jour de mois ".date("t")."<br>";
echo "Jour du mois en 2 chiffres ".date("d")."<br>"; 
echo "Jour du mois en 1 chiffre ".date("j")."<br>"; 
echo "Jour du mois en 3 lettres ".date("D")."<br>"; 
echo "Jour du mois en toutes les lettres ".date("l")."<br>"; 
echo "Indice de jour de semaine de 0 a 6 ".date("w")."<br>"; 
echo "-------------Heures-------------<br>";
echo "Heures de 1 a 12 avec am et pm : ".date("g")."<br>"; 
echo "Heures de 01 a 12 avec am et pm : ".date("h")."<br>";
echo "Heures de 1 a 23 : ".date("G")."<br>"; 
echo "Heures sur deux chiffres de 00 a 23 : ".date("H")."<br>"; 
echo "Ajouter am ou pm : ".date("a")."<br>"; 
echo "Ajouter AM ou PM : ".date("A")."<br>"; 
echo "----------Minutes et Secondes----------<br>";
echo "Minutes en 2 chiffres de 0 a 59 : ".date("i")."<br>"; 
echo "Secondes en 1 chiffre de 0 a 59 : ".date("s")."<br>"; 
echo date("l d F H Y h:i:s");
?>