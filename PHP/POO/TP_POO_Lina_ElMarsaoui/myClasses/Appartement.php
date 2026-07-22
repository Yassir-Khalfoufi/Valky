<?php
class Appartement extends Bien {
    public function __construct($id, $prix) {
        parent::__construct($id, "Appartement", $prix);
    }
}
?>