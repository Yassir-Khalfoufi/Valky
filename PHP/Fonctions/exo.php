<?php
echo "Si on utilise le format: l t F YLa date affiche serait : ".date("l t F Y")."<br>";
echo "Si on utilise le format: t/m/Y la date affiche serait : ".date("j/m/Y")."<br>";
echo "Si on utilise le format: t/m/Y la date affiche serait : ".date("D Y-m-d")."<br>";
echo "Si on utilise le format: t/m/Y la date affiche serait : ".date("j/m/Y")."<br>";


date_default_timezone_set("Africa/Casablanca");
$date1 = new DateTime();
$formatter = new IntlDateFormatter(
    'fr-FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL
);
echo $formatter ->format($date1);
echo "<br>";
echo "A cet instant le timestamp est : ",time();
$tp = time()+(23*24*3600);
echo "Dans 23 jours le timestamp seta : $tp","<br>";
$tp1 = time()-(12*24*3600);
echo "Il y a 12 jours le timestsamp etait $tp1",'<br>';
$tp2 = round(time()/3600);
echo "Le nombre d'heures depuis 1/1/1970 = $tp2","<br>";


?>