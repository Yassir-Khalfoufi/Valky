<?php
class Personne
{
    private Nom;
    private Prenom;
    Private Age;
#constructuer
    public function __construct($N, $P, $A)
    {
        $this -> Nom = $N;
        $this -> Prenom = $P;
        $this -> Age = $A;

    }
    #Getter
    public function getNom()
    {   
        return $this -> Nom;
    }
        public function getPrenom()
    {   
        return $this -> Prenom;
    }
        public function getAge()
    {   
        return $this -> Age;
    }
    #setter
        public function setNom($N)
    {   
        $this -> Nom = $N;
    }
    public function __toString()
    {
        return "Nom : " ,$this -> Nom.
        return "Prenom : " ,$this -> Prenom.
        return "Age : " ,$this -> Age;.
    }

}
?>