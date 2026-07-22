<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
requireLogin();
$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
    <link rel="stylesheet" href="/student-management/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">🎓 UniGestion</div>
    <ul class="nav-links">
        <li><a href="etudiants.php" class="<?= $currentPage === 'etudiants.php' ? 'active' : '' ?>">Étudiants</a></li>
        <li><a href="notes.php"     class="<?= $currentPage === 'notes.php'     ? 'active' : '' ?>">Notes</a></li>
        <?php if (isAdmin()): ?>
        <li><a href="utilisateurs.php" class="<?= $currentPage === 'utilisateurs.php' ? 'active' : '' ?>">Utilisateurs</a></li>
        <?php endif; ?>
    </ul>
    <div class="nav-user">
        👤 <?= e($user['nom']) ?> <span class="badge"><?= e($user['role']) ?></span>
        &nbsp;|&nbsp;
        <a href="/student-management/logout.php">Déconnexion</a>
    </div>
</nav>

<main class="container">
