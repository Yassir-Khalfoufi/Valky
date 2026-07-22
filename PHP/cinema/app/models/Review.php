<?php

class Review {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByMovie(int $movieId): array {
        $stmt = $this->db->prepare("
            select r.*, u.username
            from reviews r
            join users u on r.user_id = u.id
            where r.movie_id = :m
            order by r.created_at desc
        ");
        $stmt->execute([':m' => $movieId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, int $movieId, string $body): void {
        $stmt = $this->db->prepare("insert into reviews (user_id, movie_id, body) values (:u, :m, :b)");
        $stmt->execute([':u' => $userId, ':m' => $movieId, ':b' => $body]);
    }

    public function delete(int $reviewId, int $userId): void {
        $stmt = $this->db->prepare("delete from reviews where id = :id and user_id = :u");
        $stmt->execute([':id' => $reviewId, ':u' => $userId]);
    }
}
