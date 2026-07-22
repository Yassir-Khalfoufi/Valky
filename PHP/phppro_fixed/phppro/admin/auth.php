<?php
// admin/auth.php — include at the top of every admin page
// Usage: require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Not logged in at all → send to admin login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Logged in but not admin → kick out
if (empty($_SESSION['is_admin'])) {
    session_destroy();
    header('Location: login.php?err=access');
    exit;
}
?>
