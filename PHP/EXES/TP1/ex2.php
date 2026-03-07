<?php
    $nomComplet = "mARiem alaMI";
    echo "Le nom sans Espace a la fin et la debut : ".trim($nomComplet);
    echo "<br>Le nom en minuscule : ".strtolower($nomComplet);
    echo "<br>Le nom dont le premier caracter de chaque mot est en majuscule : ".ucwords($nomComplet);
?>