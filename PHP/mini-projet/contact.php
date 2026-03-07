<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    if (empty($nom) || empty($email) || empty($message)) {
        echo "Tous les champs sont obligatoires.";
    } else {
        echo "Merci $nom ! Votre message a été envoyé avec succès.";
    }
}
?>
