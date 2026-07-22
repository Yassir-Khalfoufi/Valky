<?php
class Immeuble extends Bien {
    public function __construct($id, $prix) {
        parent::__construct($id, "Immeuble", $prix);
    }
}
?>