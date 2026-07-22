<?php
session_start();

// Protection de la page : rediriger si non connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administration</title>
</head>
<body>
<div class="container">
    <h2>🛠 Espace Administration</h2>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['admin_login']) ?></strong> !</p>

    <div class="admin-menu">
        <h3>Gestion des Étudiants</h3>
        <a href="liste_etudiants.php" class="menu-link">📋 Liste des étudiants</a>
        <a href="ajouter_etudiant.php" class="menu-link">➕ Ajouter un étudiant</a>

        <h3>Gestion des Enseignants</h3>
        <a href="ajouter_enseignant.php" class="menu-link">➕ Ajouter un enseignant</a>

        <h3>Gestion des Soutenances</h3>
        <a href="ajouter_soutenance.php" class="menu-link">➕ Ajouter une soutenance</a>
        <a href="rechercher.php" class="menu-link">🔍 Rechercher des soutenances</a>
    </div>

    <a href="logout.php" class="btn-delete" style="margin-top:20px; display:inline-block;">
        🚪 Se déconnecter
    </a>
</div>
</body>
</html>
