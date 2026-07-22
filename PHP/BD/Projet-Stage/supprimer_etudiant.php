<?php
require_once 'dbconfig.php';

if (!isset($_GET['NCE']) || !is_numeric($_GET['NCE'])) {
    header("Location: liste_etudiants.php");
    exit;
}

$NCE = intval($_GET['NCE']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM Etudiant WHERE NCE = :NCE");
    $stmt->execute([':NCE' => $NCE]);

    header("Location: liste_etudiants.php");
    exit;
} catch (PDOException $e) {
    die("Erreur lors de la suppression : " . $e->getMessage());
}
?>
