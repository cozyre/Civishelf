<?php

class Borrow {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // -----------------------------------------------------------------------
    // Check if user has a pending or approved request for this book.
    // Prevents spamming requests.
    // -----------------------------------------------------------------------
    public function hasActiveBorrow(int $userId, int $bookId): bool {
        $stmt = $this->db->prepare(
            "SELECT request_id FROM borrow_requests
             WHERE user_id = :user_id
               AND book_id = :book_id
               AND status  IN ('pending', 'approved')
             LIMIT 1"
        );
        $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
        return (bool) $stmt->fetch();
    }

    // -----------------------------------------------------------------------
    // Insert a new borrow request with status = 'pending'.
    // Due date defaults to 14 days from now — admin can adjust.
    // -----------------------------------------------------------------------
    public function createRequest(int $userId, int $bookId): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO borrow_requests
                (user_id, book_id, status, borrow_date, due_date)
             VALUES
                (:user_id, :book_id, 'pending', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY))"
        );
        return $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
    }
}