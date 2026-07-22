<?php

class ListController {
    private MovieList $model;

    public function __construct() {
        $this->model = new MovieList();
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user'])) { header('Location: /cinema/auth/login'); exit; }
    }

    public function index(?string $p = null): void {
        $this->requireAuth();
        $lists = $this->model->getByUser($_SESSION['user']['id']);
        require 'app/views/lists/index.php';
    }

    public function show(?string $id = null): void {
        $this->requireAuth();
        $list = $this->model->getById((int) $id);
        if (!$list || $list['user_id'] != $_SESSION['user']['id']) { echo "Not found"; return; }
        $movies = $this->model->getMovies((int) $id);
        require 'app/views/lists/show.php';
    }

    public function create(?string $p = null): void {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $this->model->create($_SESSION['user']['id'], $_POST['name'], $_POST['description'] ?? '');
        }
        header('Location: /cinema/lists'); exit;
    }

    public function addMovie(?string $listId = null): void {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['movie_id'])) {
            $this->model->addMovie((int) $listId, (int) $_POST['movie_id']);
        }
        header("Location: /cinema/lists/show/$listId"); exit;
    }

    public function delete(?string $id = null): void {
        $this->requireAuth();
        $this->model->delete((int) $id, $_SESSION['user']['id']);
        header('Location: /cinema/lists'); exit;
    }
}
