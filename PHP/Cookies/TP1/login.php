<?php session_start(); ?>
<html>
<body>
<h2>Authentification</h2>
<?php 
if (isset($_SESSION['erreur'])): ?>
    <p><?= $_SESSION['erreur']; unset($_SESSION['erreur']); ?></p>
<?php endif; ?>
<form method="POST" action="traitement_login.php">
    Login: <input type="text" name="login">
    <br>
    Mot de Passe: <input type="password" name="motPasse">
    <br>
    <button type="submit">S'authentifier</button>
</form>
</body>
</html>