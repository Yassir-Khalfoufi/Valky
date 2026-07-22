<?php
class User
{
    private $Nom;
    public function __construct($N)
    {
        $this -> Nom = $N;
    }
    public function __toString()
    {
        return "Nom : ".$this -> Nom;
    }
    public function __destruct()
    {
        echo"<br>Objet Detruit";
    }
    public function __call($name, $Arguments) 
    {
        echo "La methode $name n'existe pas";
    }
    public static function __callStatic($name, $Arguments) 
    {
        echo "La methode $name n'existe pas";
    }
    public function __get($name)
    {
        echo "<br>Attribut $name inaccessible";
    }
    public function __set($name, $value)
    {
        echo "<br>impossible d'affecter la valeur $value a l'attribut $name ";
    }
    public function __isset($name)
    {
        echo"<br>Verification de l'attribut '$name'";
    }
    public function __unset($name)
    {
        echo "<br>suppression d'attribut '$name'";
    }
    public function __sleep()
    {
        echo "Preparation de la serialisation";
        return ['Nom'];
    }
    #serialization
    public function __serialize()
    {
        return['Nom' => $this->Nom];
    }
    #deserialisation
    public function __unserialize(array $data)
    {
        $this -> nom - $data['Nom'];
    }
    public function __wakeup()
    {
        echo "Restauration apres deserialisation";
    }
    public function __invoke($nom)
    {
        echo "Bonjour $nom !<br>";
    }
    public function __clone()
    {
        echo "L'objet a ete clone ! <br>";
    }

}
?>
