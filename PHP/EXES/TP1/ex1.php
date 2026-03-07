<?php
    $phrase = "PHP est un langage de script puissant et populaire.";
    echo "Le nombre de mots : ".str_word_count($phrase);
    echo "<br>Le nombre de caracteres avec espace est : ".strlen($phrase);
    $phrase_wout_space = str_replace(" ","", $phrase);
    echo "<br>Le nombre de caracteres sans espace : ".strlen($phrase_wout_space);
    $longest = '';
    foreach ($word as $phrase) {
        if (strlen($word) > strlen($longest)) {
            $longest = $word;
            echo $longest;
        }
    }


?>