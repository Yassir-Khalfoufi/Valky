<?php
class Bien {
    protected string $reference;
    protected string $type;
    protected float $surface;
    protected float $prix;
    protected string $ville;

    public function __construct(string $reference, string $type, float $surface, float $prix, string $ville) {
        $this->reference = $reference;
        $this->type = $type;
        $this->surface = $surface;
        $this->prix = $prix;
        $this->ville = $ville;
    }

    public function getReference(): string { return $this->reference; }
    public function getType(): string { return $this->type; }
    public function getSurface(): float { return $this->surface; }
    public function getPrix(): float { return $this->prix; }
    public function getVille(): string { return $this->ville; }

    public function setReference(string $v): self { $this->reference = $v; return $this; }
    public function setType(string $v): self { $this->type = $v; return $this; }
    public function setSurface(float $v): self { $this->surface = $v; return $this; }
    public function setPrix(float $v): self { $this->prix = $v; return $this; }
    public function setVille(string $v): self { $this->ville = $v; return $this; }

    public function afficher(): void {
        echo "Ref: {$this->reference} | Type: {$this->type} | Surface: {$this->surface}m² | Prix: {$this->prix} MAD | Ville: {$this->ville}";
    }
}
