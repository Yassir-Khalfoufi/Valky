<?php
// includes/db.php — Connexion PDO à la base de données

define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_etudiants');
define('DB_USER', 'root');       // Modifier selon votre configuration
define('DB_PASS', '');           // Modifier selon votre configuration
define('DB_CHARSET', 'utf8mb4');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<p style="color:red;font-family:sans-serif;">Erreur de connexion : ' . htmlspecialchars($e->getMessage()) . '</p>');
        }
    }
    return $pdo;
}
