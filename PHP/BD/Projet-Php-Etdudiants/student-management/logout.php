<?php
// logout.php
require_once 'includes/auth.php';
startSession();
session_destroy();
header('Location: index.php');
exit;
