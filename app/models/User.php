<?php

class User {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Find a user by email. Returns the row or false.
     */
    public function findByEmail(string $email) {
        $this->db->query('SELECT * FROM users WHERE email = :email LIMIT 1');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id) {
        $this->db->query('SELECT * FROM users WHERE id = :id LIMIT 1');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Check whether an email is already registered.
     */
    public function emailExists(string $email): bool {
        $this->db->query('SELECT id FROM users WHERE email = :email LIMIT 1');
        $this->db->bind(':email', $email);
        return (bool) $this->db->single();
    }

    /**
     * Insert a new user. Returns the new user's ID on success, false on failure.
     */
    public function create(string $name, string $email, string $password) {
        $this->db->query(
            'INSERT INTO users (name, email, password, role, created_at)
             VALUES (:name, :email, :password, "user", NOW())'
        );
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', password_hash($password, PASSWORD_DEFAULT));

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Verify a plain-text password against the stored hash.
     */
    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    // -----------------------------------------------------------------------
    // Admin methods (admins can live in a separate table or use role column)
    // -----------------------------------------------------------------------

    /**
     * Find an admin by email.
     * If you have a separate `admins` table, change the query accordingly.
     */
    public function findAdminByEmail(string $email) {
        $this->db->query(
            "SELECT * FROM users WHERE email = :email AND role = 'admin' LIMIT 1"
        );
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
}