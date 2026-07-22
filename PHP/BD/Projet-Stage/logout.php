<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = [];

// Détruire le cookie de session si existant
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Rediriger vers l'accueil
header("Location: index.php?logout=1");
exit;
?>
