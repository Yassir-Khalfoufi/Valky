<?php
// auth.php — Middleware d'authentification
require_once __DIR__ . '/db.php';

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('REMEMBER_COOKIE', 'cinelist_remember');
define('REMEMBER_DAYS',   30);

function requireLogin(): array {
    if (!empty($_SESSION['user_id'])) {
        return ['id' => $_SESSION['user_id'], 'username' => $_SESSION['username']];
    }
    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $user = checkRememberCookie($_COOKIE[REMEMBER_COOKIE]);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            setRememberCookie($user['id']);
            return $user;
        }
        clearRememberCookie();
    }
    header('Location: login.php');
    exit;
}

function checkRememberCookie(string $rawToken): ?array {
    $pdo  = getPDO();
    $hash = hash('sha256', $rawToken);
    $stmt = $pdo->prepare('
        SELECT u.id, u.username
        FROM remember_tokens rt
        JOIN users u ON u.id = rt.user_id
        WHERE rt.token = :token AND rt.expires_at > NOW()
        LIMIT 1
    ');
    $stmt->execute([':token' => $hash]);
    return $stmt->fetch() ?: null;
}

function setRememberCookie(int $userId): void {
    $pdo      = getPDO();
    $rawToken = bin2hex(random_bytes(32));
    $hash     = hash('sha256', $rawToken);
    $expires  = date('Y-m-d H:i:s', time() + 86400 * REMEMBER_DAYS);

    $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = :uid')
        ->execute([':uid' => $userId]);

    $pdo->prepare('INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (:uid, :tok, :exp)')
        ->execute([':uid' => $userId, ':tok' => $hash, ':exp' => $expires]);

    setcookie(REMEMBER_COOKIE, $rawToken, [
        'expires'  => time() + 86400 * REMEMBER_DAYS,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clearRememberCookie(): void {
    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $hash = hash('sha256', $_COOKIE[REMEMBER_COOKIE]);
        try {
            getPDO()->prepare('DELETE FROM remember_tokens WHERE token = :t')
                    ->execute([':t' => $hash]);
        } catch (Exception) {}
    }
    setcookie(REMEMBER_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function loginUser(array $user, bool $remember): void {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    if ($remember) setRememberCookie($user['id']);
}

function logoutUser(): void {
    clearRememberCookie();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
