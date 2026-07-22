<?php

function demarrer_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function est_connecte(): bool {
    demarrer_session();
    return isset($_SESSION['user_id']);
}

function exiger_connexion(): void {
    if (!est_connecte()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function utilisateur_connecte(): array {
    demarrer_session();
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'nom'   => $_SESSION['user_nom']  ?? '',
        'role'  => $_SESSION['user_role'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
    ];
}

function est_admin(): bool {
    return utilisateur_connecte()['role'] === 'admin';
}

function deconnecter(): void {
    demarrer_session();
    session_destroy();
    header('Location: /auth/login.php');
    exit;
}

function set_flash(string $type, string $message): void {
    demarrer_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    demarrer_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
