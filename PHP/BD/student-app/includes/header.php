<?php
// includes/header.php
// appelé avec : require_once __DIR__ . '/../includes/header.php';
// Variable $titre doit être définie avant l'inclusion.
$flash = get_flash();
$user  = utilisateur_connecte();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre ?? 'Gestion Étudiants') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="/etudiants/liste.php" class="logo">&#x1F393; UniGest</a>

        <?php if (est_connecte()): ?>
        <nav>
            <a href="/etudiants/liste.php">Étudiants</a>
            <a href="/etudiants/ajouter.php">+ Ajouter</a>
            <a href="/auth/logout.php" class="btn-logout">Déconnexion</a>
        </nav>
        <span class="user-badge"><?= htmlspecialchars($user['nom']) ?> (<?= $user['role'] ?>)</span>
        <?php endif; ?>
    </div>
</header>

<main class="container">

<?php if ($flash): ?>
<div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>
