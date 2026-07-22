<?php
session_start();

// Si déjà connecté, rediriger vers l'espace admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil – Gestion des Stages</title>
</head>
<body>
<div class="container login-box">
    <h1>🎓 Gestion des Stages</h1>
    <h2>Connexion Administrateur</h2>

    <?php if (isset($_GET['error'])): ?>
        <p class="error">⚠ Login ou mot de passe incorrect.</p>
    <?php endif; ?>
    <?php if (isset($_GET['logout'])): ?>
        <p class="success">✔ Vous avez été déconnecté.</p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Login :</label>
        <input type="text" name="login" required autofocus>

        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>

        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
