<?php
require 'connexion.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE * FROM joueurs WHERE id = :id");
$stmt -> execute([':id' => $id]);

header("Location: liste.php");
exit;
?>