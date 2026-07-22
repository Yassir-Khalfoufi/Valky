<?php

class AuthController {
    private User $model;

    public function __construct() {
        $this->model = new User();
    }

    public function login(?string $p = null): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->model->findByEmail($_POST['email'] ?? '');
            if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: /cinema/movies'); exit;
            }
            $error = 'Invalid credentials';
        }
        require 'app/views/auth/login.php';
    }

    public function register(?string $p = null): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->create($_POST['username'], $_POST['email'], $_POST['password']);
                header('Location: /cinema/auth/login'); exit;
            } catch (PDOException $e) {
                $error = 'Username or email already taken';
            }
        }
        require 'app/views/auth/register.php';
    }

    public function logout(?string $p = null): void {
        session_destroy();
        header('Location: /cinema/auth/login'); exit;
    }
}
