<?php

session_start();
require_once 'config/database.php';

spl_autoload_register(function (string $class): void {
    foreach (['app/models/', 'app/controllers/'] as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

$url   = trim($_GET['url'] ?? 'movies', '/');
$parts = explode('/', $url);

$controllerName = ucfirst($parts[0] ?? 'movies') . 'Controller';
$action         = $parts[1] ?? 'index';
$param          = $parts[2] ?? null;

$controllerFile = "app/controllers/{$controllerName}.php";
if (!file_exists($controllerFile)) {
    http_response_code(404); echo "404 Not Found"; exit;
}

require_once $controllerFile;
$controller = new $controllerName();

// if (!method_exists($controller, $action)) {
    // http_response_code(404); echo "404 Not Found"; exit;
// }

$controller->$action($param);
