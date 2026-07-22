<?php

class Employe 
{
    private $matricule;
    private $nom;
    private $prenom;
    private $dateNaissance;
    private $dateEmbauche;
    private $salaire;

    public function __construct($matricule, $nom, $prenom, $dateNaissance, $dateEmbauche, $salaire) {
        $this->matricule = $matricule;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->dateEmbauche = $dateEmbauche;
        $this->salaire = $salaire;
    }

    public function Age() {
        return (new DateTime())->diff(new DateTime($this->dateNaissance))->y;
    }

    public function Anciennete() {
        return (new DateTime())->diff(new DateTime($this->dateEmbauche))->y;
    }

    public function AugmentationDuSalaire() {
        $anciennete = $this->Anciennete();

        if ($anciennete < 5) {
            $taux = 0.02;
        } elseif ($anciennete < 10) {
            $taux = 0.05;
        } else {
            $taux = 0.10;
        }

        $this->salaire += $this->salaire * $taux;
    }

    public function AfficherEmploye() {
        echo "-------------------------------<br>";
        echo "Matricule   : $this->matricule<br>";
        echo "Nom complet : " . strtoupper($this->nom) . " " . ucfirst(strtolower($this->prenom)) . "<br>";
        echo "Age         : " . $this->Age() . " ans<br>";
        echo "Ancienneté  : " . $this->Anciennete() . " ans<br>";
        echo "Salaire     : " . number_format($this->salaire, 2) . " DH<br>";
        echo "-------------------------------<br>";
    }
}
$E1 = new Employe("E001", "khalfoufi", "yassir", "1995-08-02", "2015-08-02", 8000);

echo "=== Avant augmentation <br>";
$E1->AfficherEmploye();

$E1->AugmentationDuSalaire();

echo "\n=== Après augmentation ===\n";
$E1->AfficherEmploye();

?>