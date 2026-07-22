<?php
class Locations
{
    private $id;
    private $user;
    private $client;
    private $datedebut;
    private $nombreJour;
    private $datefin;
    private $prix;

    public function __construct($id, $user, $client, $datedebut, $nombreJour, $datefin, $prix)
    {
        $this -> id = $id;
        $this -> user = $user;
        $this -> client = $client;
        $this -> datedebut = $datedebut;
        $this -> nombreJour = $nombreJour;
        $this -> datefin = $datefin;
        $this -> prix = $prix;
    }
    public function getId()
        {return $this->id;}
    public function setId()
        {$this-> id;}
    public function getUser()
        {return $this -> user;}
    public function setUser()
        {$this -> user;}
    public function getClient()
        {return $this-> client;}
    public function setClient()
        {$this -> client;}
    public function getDatedebut()
        {return $this -> datedebut;}
    public function setDatedebut()
        {$this -> datedebut;}
    public function Calculer_datefin()
        {
            $this->datefin = date("y-m-d",
            strtotime($this-> datedebut. "+" .$this->nombreJour));
        }
    public function getTotal()
        {
            return $this -> nombreJour * $this -> prix;
        }
}
?>