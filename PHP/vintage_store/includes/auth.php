<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function currentUser(): array|null {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role'     => $_SESSION['role'],
    ];
}

function flashMessage(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): array|null {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
