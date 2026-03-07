<?php
    $phrase = "Bienvenue sur le site officiel de notre projet PHP.";
    echo "La phrase commnencent par site : ".strpos($phrase, "site");
    $cps = substr($phrase,3,0);
    echo $cps;
?>