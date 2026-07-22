<?php
// logout.php
require_once __DIR__ . '/includes/auth.php';

$_SESSION = [];
session_destroy();

// Redirect to home with a flash — re-start session briefly for flash
session_start();
$_SESSION['flash'] = ['type' => 'success', 'msg' => 'You have been logged out.'];
header('Location: /index.php');
exit;
