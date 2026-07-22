<?php

class Movie {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(string $search = ''): array {
        if ($search) {
            $stmt = $this->db->prepare("select * from movies where title like :s or director like :s2 order by title");
            $like = "%$search%";
            $stmt->execute([':s' => $like, ':s2' => $like]);
        } else {
            $stmt = $this->db->query("select * from movies order by title");
        }
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("select * from movies where id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getUserMovieData(int $userId, int $movieId): ?array {
        $stmt = $this->db->prepare("select * from user_movies where user_id = :u and movie_id = :m");
        $stmt->execute([':u' => $userId, ':m' => $movieId]);
        return $stmt->fetch() ?: null;
    }

    public function setStatus(int $userId, int $movieId, string $status, ?int $rating): void {
        $stmt = $this->db->prepare("
            insert into user_movies (user_id, movie_id, status, rating) values (:u, :m, :s, :r)
            on duplicate key update status = values(status), rating = coalesce(values(rating), rating)
        ");
        $stmt->execute([':u' => $userId, ':m' => $movieId, ':s' => $status, ':r' => $rating]);
    }

    public function getAverageRating(int $movieId): ?string {
        $stmt = $this->db->prepare("select round(avg(rating), 1) as avg from user_movies where movie_id = :m and rating is not null");
        $stmt->execute([':m' => $movieId]);
        return $stmt->fetchColumn() ?: null;
    }
}
