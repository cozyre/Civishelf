<?php

class News {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all news articles, newest first.
     */
    public function getAll(): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM news ORDER BY created_at DESC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get N most recent news articles.
     */
    public function getRecent(int $limit = 6): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM news ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Total books in the library (sum of total_copies).
     */
    public function getTotalBooks(): int {
        $stmt = $this->db->query('SELECT SUM(total_copies) FROM books');
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total books currently borrowed
     * = sum of (total_copies - available_copies) across all books.
     */
    public function getTotalBorrowed(): int {
        $stmt = $this->db->query(
            'SELECT SUM(total_copies - available_copies) FROM books'
        );
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total registered (non-banned) users.
     */
    public function getTotalUsers(): int {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM users WHERE user_status = 'active'"
        );
        return (int) $stmt->fetchColumn();
    }
}