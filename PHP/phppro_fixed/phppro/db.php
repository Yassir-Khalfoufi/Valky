<?php
// db.php — database connection (include this in every PHP page)
// Edit the credentials below to match your server

define('DB_HOST', 'localhost');
define('DB_NAME', 'vintage_store');
define('DB_USER', 'root');      // change to your MySQL username
define('DB_PASS', '');          // change to your MySQL password
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
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
?>
