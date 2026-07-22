<?php
session_start();
require 'connexion.php';

$login = $_POST['login'] ?? '';
$motPasse = $_POST['motPasse'] ?? '';

// a. champs vides
if (empty($login) || empty($motPasse)) {
    $_SESSION['erreur'] = "Veuillez saisir un login et un mot de passe.";
    header("Location: login.php");
    exit;
}

// b. vérifier dans la base
$stmt = $conn->prepare("SELECT * FROM CompteProprietaire WHERE loginProp = ? AND motPasse = ?");
$stmt->execute([$login, $motPasse]);
$proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proprietaire) {
    $_SESSION['erreur'] = "Erreur de login/mot de passe.";
    header("Location: login.php");
    exit;
}

// c. login correct → créer session
$_SESSION['login'] = $proprietaire['loginProp'];
$_SESSION['nom'] = $proprietaire['nom'];
$_SESSION['prenom'] = $proprietaire['prenom'];
header("Location: accueil.php");
exit;
?>