<?php
class Eq2Degre {
    private float $a;
    private float $b;
    private float $c;
    private array $solutions = [];

    public function __construct(float $a, float $b, float $c) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function getDiscriminant(): float {
        return ($this->b ** 2) - 4 * $this->a * $this->c;
    }

    public function afficheDiscriminant(): void {
        echo "Discriminant = " . $this->getDiscriminant();
    }

    public function resoudre(): array {
        $d = $this->getDiscriminant();
        $this->solutions = [];
        if ($d > 0) {
            $this->solutions[] = (-$this->b + sqrt($d)) / (2 * $this->a);
            $this->solutions[] = (-$this->b - sqrt($d)) / (2 * $this->a);
        } elseif ($d == 0) {
            $this->solutions[] = -$this->b / (2 * $this->a);
        }
        return $this->solutions;
    }

    public function afficheSolutions(): void {
        $sols = $this->resoudre();
        $d = $this->getDiscriminant();
        if ($d > 0) {
            echo "Deux solutions : x1=" . $sols[0] . ", x2=" . $sols[1];
        } elseif ($d == 0) {
            echo "Une solution : x=" . $sols[0];
        } else {
            echo "Pas de solution réelle.";
        }
    }
}
