<?php
// etudiants/supprimer.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

exiger_connexion();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'Identifiant invalide.');
    header('Location: /etudiants/liste.php');
    exit;
}

try {
    $stmt = $pdo->prepare('delete from etudiants where id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Étudiant supprimé avec succès.');
    } else {
        set_flash('error', 'Étudiant introuvable.');
    }
} catch (PDOException $e) {
    set_flash('error', 'Erreur lors de la suppression.');
}

header('Location: /etudiants/liste.php');
exit;
