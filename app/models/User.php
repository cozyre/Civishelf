<?php

class User {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // -----------------------------------------------------------------------
    // Find a user by email. Returns associative array or false.
    // -----------------------------------------------------------------------
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // -----------------------------------------------------------------------
    // Find a user by ID.
    // -----------------------------------------------------------------------
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // -----------------------------------------------------------------------
    // Check whether an email is already registered.
    // -----------------------------------------------------------------------
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetch();
    }

    // -----------------------------------------------------------------------
    // Insert a new user. Returns new user_id on success, false on failure.
    // -----------------------------------------------------------------------
    public function create(string $name, string $email, string $password): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO users (user_name, email, password_hash, role, user_status, created_at)
             VALUES (:name, :email, :password, 'user', 'active', NOW())"
        );
        $ok = $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    // -----------------------------------------------------------------------
    // Verify a plain-text password against a stored hash.
    // -----------------------------------------------------------------------
    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    // -----------------------------------------------------------------------
    // Admin-specific lookup — only returns rows where role = 'admin'.
    // -----------------------------------------------------------------------
    public function findAdminByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email AND role = 'admin' LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
}