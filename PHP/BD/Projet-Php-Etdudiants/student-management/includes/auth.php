<?php
// includes/auth.php — Gestion de la session et sécurité

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false, // Mettre true en production (HTTPS)
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'nom'  => $_SESSION['user_nom']  ?? '',
        'role' => $_SESSION['user_role'] ?? '',
    ];
}

function isAdmin(): bool {
    return (currentUser()['role'] === 'admin');
}

/**
 * Génère un token CSRF et le stocke en session.
 */
function csrfToken(): string {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF envoyé via POST.
 */
function verifyCsrf(): void {
    startSession();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('Token CSRF invalide.');
    }
}

/**
 * Échappe une valeur pour l'affichage HTML (protection XSS).
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
