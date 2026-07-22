<?php

class MovieController {
    private Movie $model;

    public function __construct() {
        $this->model = new Movie();
    }

    public function index(?string $p = null): void {
        $search = $_GET['q'] ?? '';
        $movies = $this->model->getAll($search);
        require 'app/views/movies/index.php';
    }

    public function show(?string $id = null): void {
        $movie = $this->model->getById((int) $id);
        if (!$movie) { echo "Movie not found"; return; }

        $reviewModel = new Review();
        $reviews  = $reviewModel->getByMovie((int) $id);
        $avg      = $this->model->getAverageRating((int) $id);
        $userMovie = null;

        if (isset($_SESSION['user'])) {
            $userMovie = $this->model->getUserMovieData($_SESSION['user']['id'], (int) $id);
        }
        require 'app/views/movies/show.php';
    }

    public function status(?string $id = null): void {
        if (!isset($_SESSION['user'])) { header('Location: /cinema/auth/login'); exit; }
        $status = in_array($_POST['status'] ?? '', ['watchlist', 'watched']) ? $_POST['status'] : 'watchlist';
        $rating = !empty($_POST['rating']) ? (int) $_POST['rating'] : null;
        $this->model->setStatus($_SESSION['user']['id'], (int) $id, $status, $rating);
        header("Location: /cinema/movies/show/$id"); exit;
    }
}
