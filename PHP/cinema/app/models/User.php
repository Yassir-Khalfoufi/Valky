<?php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(string $username, string $email, string $password): bool {
        $stmt = $this->db->prepare("insert into users (username, email, password) values (:u, :e, :p)");
        return $stmt->execute([':u' => $username, ':e' => $email, ':p' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("select * from users where email = :e");
        $stmt->execute([':e' => $email]);
        return $stmt->fetch() ?: null;
    }
}
