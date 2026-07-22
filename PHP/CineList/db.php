<?php
// db.php — Connexion PDO
define('DB_HOST',    'localhost');
define('DB_NAME',    'cinelist');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn     = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:monospace;padding:2rem;background:#1a0a0a;color:#ff4444;border:1px solid #ff4444;">
                <strong>Erreur PDO :</strong><br>' . htmlspecialchars($e->getMessage()) . '
                <br><br>Vérifiez vos identifiants dans <code>db.php</code>.
            </div>');
        }
    }
    return $pdo;
}
