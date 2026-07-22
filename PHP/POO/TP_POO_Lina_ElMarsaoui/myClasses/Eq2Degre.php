<?php
class Eq2Degre {
    private $a;
    private $b;
    private $c;
    private $solutions = [];

    public function __construct($a, $b, $c) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function getDiscriminant() {
        return ($this->b * $this->b) - (4 * $this->a * $this->c);
    }

    public function afficheDiscriminant() {
        echo $this->getDiscriminant();
    }

    public function resoudre() {
        $d = $this->getDiscriminant();

        if ($d > 0) {
            $x1 = (-$this->b - sqrt($d)) / (2 * $this->a);
            $x2 = (-$this->b + sqrt($d)) / (2 * $this->a);
            $this->solutions = [$x1, $x2];
        } elseif ($d == 0) {
            $x = (-$this->b) / (2 * $this->a);
            $this->solutions = [$x];
        } else {
            $this->solutions = [];
        }

        return $this->solutions;
    }

    public function afficheSolutions() {
        $sol = $this->resoudre();
        foreach ($sol as $s) {
            echo $s . "<br>";
        }
    }
}
?>