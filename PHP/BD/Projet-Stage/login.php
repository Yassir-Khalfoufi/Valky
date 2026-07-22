<?php
session_start();
require_once 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$login       = trim($_POST['login']);
$mot_de_passe = trim($_POST['mot_de_passe']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification du login et mot de passe (hashé en SHA-256)
    $stmt = $pdo->prepare("SELECT * FROM Administrateur WHERE login = :login AND mot_de_passe = SHA2(:mot_de_passe, 256)");
    $stmt->execute([':login' => $login, ':mot_de_passe' => $mot_de_passe]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login']     = $admin['login'];
        $_SESSION['admin_id']        = $admin['id_admin'];
        header("Location: admin.php");
        exit;
    } else {
        header("Location: index.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
