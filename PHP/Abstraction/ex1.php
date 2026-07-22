<?php
abstract class Employe
{
    private $Nom;
    private $Prenom;
    
    public function __construct($N, $P)
    {
        $this -> Nom= $N;
        $this -> Prenom = $P;
    }
    public function getNom()
    {
        return $this -> Nom;
    }
    public function setNom($N)
    {
        $this -> Nom= $N;
    }
    public function getPreom()
    {
        return $this -> Prenom;
    }
    public function setPrenom($P)
    {
        $this -> Prenom= $P;
    }
    public function __toString()
    {
        return "Nom : ".$this ->Nom.
        "  Prenom : ".$this ->Prenom;
    }
    abstract public function gains();
}
class Patron extends Employe
{
    private $Salaire;
    public function __construct($Nom, $Prenom, $S)
    {
        parent::__construct($Nom, $Prenom);
        $this ->Salaire = $S;
    }
    public function getSalaire()
    {
        return $this -> Salaire;
    }
    public function setSalaire($Salaire)
    {
        $this -> Salaire= $Salaire;
    }
    public function __toString()
    {
        return "Patron : ".parent::__toString();
    }
    public function gains()
    {
        return $this -> Salaire;
    }
}
class TravailleurCommission extends Employe
{
    private $Salaire;
    private $Commission;
    private $Quantite;
    public function __construct($Nom, $Prenom, $Salaire, $Commission, $Quantite)
    {
        parent::__construct($Nom, $Prenom);
        $this -> Salaire= $Salaire;
        $this -> Commission = $Commission;
        $this -> Quantite = $Quantite;   
    }
}
?>