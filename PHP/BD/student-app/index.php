<?php
// index.php — point d'entrée
require_once __DIR__ . '/includes/auth.php';
demarrer_session();

if (est_connecte()) {
    header('Location: /etudiants/liste.php');
} else {
    header('Location: /auth/login.php');
}
exit;
