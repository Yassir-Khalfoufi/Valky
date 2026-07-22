<?php
class Appartement extends Bien {
    private int $etage;
    private int $nbPieces;

    public function __construct(string $ref, float $surface, float $prix, string $ville, int $etage, int $nbPieces) {
        parent::__construct($ref, 'Appartement', $surface, $prix, $ville);
        $this->etage = $etage;
        $this->nbPieces = $nbPieces;
    }

    public function getEtage(): int { return $this->etage; }
    public function getNbPieces(): int { return $this->nbPieces; }
    public function setEtage(int $v): self { $this->etage = $v; return $this; }
    public function setNbPieces(int $v): self { $this->nbPieces = $v; return $this; }

    public function afficher(): void {
        parent::afficher();
        echo " | Etage: {$this->etage} | Pièces: {$this->nbPieces}";
    }
}
