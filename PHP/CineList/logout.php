<?php
require_once 'auth.php';
logoutUser();
header('Location: login.php');
exit;
