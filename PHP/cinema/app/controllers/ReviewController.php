<?php

class ReviewController {
    private Review $model;

    public function __construct() {
        $this->model = new Review();
    }

    public function create(?string $movieId = null): void {
        if (!isset($_SESSION['user'])) { header('Location: /cinema/auth/login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {
            $this->model->create($_SESSION['user']['id'], (int) $movieId, $_POST['body']);
        }
        header("Location: /cinema/movies/show/$movieId"); exit;
    }

    public function delete(?string $id = null): void {
        if (!isset($_SESSION['user'])) { header('Location: /cinema/auth/login'); exit; }
        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $this->model->delete((int) $id, $_SESSION['user']['id']);
        header("Location: /cinema/movies/show/$movieId"); exit;
    }
}
