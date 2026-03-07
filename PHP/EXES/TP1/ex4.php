<?php
    $mot = "Radar";
    $igc = strtolower($mot);
    echo "Str dont la casse est ignorable : ".$igc;
    $str = strrev($igc);
    if ($igc == $str)
        {
            echo'<br>Radar est palindrome .';
        }
?>