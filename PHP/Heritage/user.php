<?php
require("administrateur.php");
class Utilisateur extends Administrateur implements Admin
{
    private $Service;
    public function __construct($Nom, $Prenom, $S)
    {
        parent::__construct($Nom, $Prenom);
        $this ->Service = $S;
    }
    public function __toString()
    {
        return "Nom : ".$this ->Nom.
            "  Prenom : ".$this ->Prenom.
            "  Vie : ".self::VIE.
            "  Service : ".$this ->Service;
    }
    public function Affichage(){
        echo"Methode abstraite";
    }
}
?>