<?php
class Terrain extends Bien {
    private bool $constructible;

    public function __construct(string $ref, float $surface, float $prix, string $ville, bool $constructible) {
        parent::__construct($ref, 'Terrain', $surface, $prix, $ville);
        $this->constructible = $constructible;
    }

    public function isConstructible(): bool { return $this->constructible; }
    public function setConstructible(bool $v): self { $this->constructible = $v; return $this; }

    public function afficher(): void {
        parent::afficher();
        echo " | Constructible: " . ($this->constructible ? 'Oui' : 'Non');
    }
}
