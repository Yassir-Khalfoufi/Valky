<?php
    abstract class Administrateur
    {
        protected $Nom;
        protected $Prenom;
        const VIE= 80; #attribut static

        public function __construct($N, $P)
        {
            $this ->Nom = $N;
            $this ->Prenom = $P;
        }
        #getter
        public function getNom()
        {
            return $this -> Nom;
        }
        #setter
        public function setNom($N)
        {
            $this ->Nom = $N;
        }
        public function __toString()
        {
        return "Nom : ".$this ->Nom.
            "  Prenom : ".$this ->Prenom.
            "  Vie : ".self::VIE;
        }
        abstract public static function Afficher()
        {
            echo"<br>Moyenne de vie est : ".self::VIE;
        }
    }
?>