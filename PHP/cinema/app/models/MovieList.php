<?php

class MovieList {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare("
            select l.*, count(lm.movie_id) as count
            from lists l
            left join list_movies lm on l.id = lm.list_id
            where l.user_id = :u
            group by l.id
            order by l.created_at desc
        ");
        $stmt->execute([':u' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("select * from lists where id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getMovies(int $listId): array {
        $stmt = $this->db->prepare("
            select m.* from movies m
            join list_movies lm on m.id = lm.movie_id
            where lm.list_id = :id
        ");
        $stmt->execute([':id' => $listId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $name, string $desc): int {
        $stmt = $this->db->prepare("insert into lists (user_id, name, description) values (:u, :n, :d)");
        $stmt->execute([':u' => $userId, ':n' => $name, ':d' => $desc]);
        return (int) $this->db->lastInsertId();
    }

    public function addMovie(int $listId, int $movieId): void {
        $stmt = $this->db->prepare("insert ignore into list_movies (list_id, movie_id) values (:l, :m)");
        $stmt->execute([':l' => $listId, ':m' => $movieId]);
    }

    public function delete(int $listId, int $userId): void {
        $stmt = $this->db->prepare("delete from lists where id = :id and user_id = :u");
        $stmt->execute([':id' => $listId, ':u' => $userId]);
    }
}
