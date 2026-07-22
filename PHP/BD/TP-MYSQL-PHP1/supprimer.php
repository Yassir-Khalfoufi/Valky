<?php
include "db.php";
$stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = ?");
$stmt->execute([$_GET['id']]);
header("Location: liste.php");
exit;
?>
