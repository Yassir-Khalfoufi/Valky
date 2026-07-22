<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
require 'connexion.php';

$libelle     = $_POST['libelle'];
$prixUnitaire = $_POST['prixUnitaire'];
$dateAchat   = $_POST['dateAchat'];
$idCategorie = $_POST['idCategorie'];

$nomPhoto = null;
if ($_FILES['photoProduit']['error'] === 0) {
    $nomPhoto = basename($_FILES['photoProduit']['name']);
    move_uploaded_file($_FILES['photoProduit']['tmp_name'], "images/" . $nomPhoto);
}


$stmt = $conn->prepare("INSERT INTO Produit (libelle, prixUnitaire, dateAchat, photoProduit, idCategorie) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$libelle, $prixUnitaire, $dateAchat, $nomPhoto, $idCategorie]);


header("Location: accueil.php");
exit;
?>