<?php
class Immeuble extends Bien {
    private int $nbEtages;
    private int $nbAppartements;

    public function __construct(string $ref, float $surface, float $prix, string $ville, int $nbEtages, int $nbAppartements) {
        parent::__construct($ref, 'Immeuble', $surface, $prix, $ville);
        $this->nbEtages = $nbEtages;
        $this->nbAppartements = $nbAppartements;
    }

    public function getNbEtages(): int { return $this->nbEtages; }
    public function getNbAppartements(): int { return $this->nbAppartements; }
    public function setNbEtages(int $v): self { $this->nbEtages = $v; return $this; }
    public function setNbAppartements(int $v): self { $this->nbAppartements = $v; return $this; }

    public function afficher(): void {
        parent::afficher();
        echo " | Etages: {$this->nbEtages} | Appartements: {$this->nbAppartements}";
    }
}
