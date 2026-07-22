<?php
class Bien {
    protected $id;
    protected $type;
    protected $prix;

    public function __construct($id, $type, $prix) {
        $this->id = $id;
        $this->type = $type;
        $this->prix = $prix;
    }

    public function afficher() {
        echo $this->id . "<br>";
        echo $this->type . "<br>";
        echo $this->prix . "<br>";
    }
}
?>