<?php
    $Semaine = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
    echo $Semaine[0];

    $Semaine[2] = "MONday"; //Modification
    echo $Semaine[2];

    $Semaine3 = array();
    $Semaine3[0] = " klab";
    $Semaine3[1] = "klab";
    $Semaine3[2] = "klab";
    $Semaine3[3] = "klab";
    $Semaine3[4] = "klab";
    $Semaine3[5] = "klab";
    $Semaine3[6] = "klab";
    echo $Semaine3[0];
    echo $Semaine3[7];
    //print everything with for
    echo"-=-=-Boucle for";
    echo "<br>";
    for($i = 0 ; $i < count(value:$Semaine); $i++)
        {
            echo $Semaine[$i]. "<br>";
            echo "<br>";
        }
    //print everythin with while
    $i = 0;
    echo "-=-=-=Boucle while=-=-=-";
    echo "<br>";
    while($i < count(value:$Semaine))
    {
        echo $Semaine[$i]."<br>";
        $i++;
        echo "<br>";
    }
    //print eveyrthin with foreach
    echo "-=-=-=Boucle foreach=-=-=-";
    echo "<br>";
    foreach($Semaine as $jour)
        {
            echo $jour. "<br><br>";  
        }
    //associatif array 
    $Semaine4 = array(
        "Jour1"=> "Lundi","Jour2"=> "Mardi","Jour3"=> "Mercredi",
    );
    echo $Semaine4["Jour2"]; //prinitn by key
//Associatif array
    $Semiane5 = array();
    $Semaine["Jour1"] = "Lundi";
    $Semaine["Jour2"] = "Mardi";
    $Semaine["Jour3"] = "Mercredi";
    $Semaine["Jour4"] = "Jeudi";
    $Semaine["Jour5"] = "Vendredi";
    $Semaine["Jour6"] = "Samedi";
    $Semaine["Jour7"] = "Dimanche";
    foreach($Semaine5 as $jour => $cle) //affiche valeur
        {
             echo $jour."</br>";
        }
    foreach($Semain5 as $jour => $cle)
        {

        }

$tableau = array (1337,42.4,"TREZ","Payton",false,1337);
//ad elements to the end of the array
// array_push($tableau,0,"y","k",false);
// to the start of the array
// array_unshift($tableau,0,"klb",false);

foreach($tableau as $value)
    {
        echo $value."<br>";
    }
//deletes last element in an array
array_pop($tableau);
//delete first element
array_shift($tableau);
array_splice($tableau,0,1);
array_slice($tableau,2,4);
sort(($tableau)); //ordre croissant
rsort($tableau); //ordre decroissant
$count = array_count_values($tableau);
echo "".$count."";

$a = [1,2,3,4,5];
$b = [3,4,5];
$table2 = array_diff($a, $b);
$table3 = array_intersect($a, $b);
echo $table2, $table3;
$apprenants = array
(
    array("zineb azhary",10,11),
    array("mknk",11,12),
    array("klb",12,13),
    array("mlkn",13,0),
);
?>
<table>
<tr>
    <td>DEv103</td>
    <td>isgi</td>
    <td>1337</td>
</tr>
</table>
foreach($apprenants as $apprenant)
    {
        foreach($apprenant as $elt)
            {
                echo $elt ."| ";
            }
    }

?>