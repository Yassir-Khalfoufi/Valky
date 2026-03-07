<?php
//func w/o parameter and return
    function Msg(){
        echo "Bonjour";
    }
    Msg();
//a func with parameters
function Somme($a, $b){
    return $a + $b;
}
echo Somme(13, 37);
//func with default value
function Somme1($a, $b = 2){
    return $a + $b;
}
echo Somme1(13);
$x = 15; //variable globale
function test(){
    $y = 2; //variable local
    global $x;
    echo"La valeur de x est: $x <br>";
}
test();
//annonymous funcs (lambda)
$bonjour = function($nom) {
    return "Bonjour" . $nom;
};
echo $bonjour(nom: " Dev 103");
function addition (int $a,  int $b): int{
    return $a + $b;
}
echo addition(2,9);
//type nullable
function saluer(?string $nom): string {
    if ($nom === null){
        return "Bonjour invite<br>";
    }
    return "Bonjour" . $nom."<br>";
}
echo saluer("Ali");
echo saluer(null);
//focntion recursive
function facto($n){
    if ($n==1) return 1;
    else{
        return $n*facto($n-1);
    }
}
echo "<br>Factorielle =",facto(5);
?>

