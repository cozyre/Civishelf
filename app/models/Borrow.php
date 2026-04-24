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

    // -----------------------------------------------------------------------
    // Mark a borrow as returned and restore available_copies.
    // Security: requires user_id so users can only return their own borrows.
    // Wrapped in a transaction — same pattern as Admin::returnBorrow().
    // -----------------------------------------------------------------------
    public function returnRequest(int $requestId, int $userId): bool {
        $this->db->beginTransaction();
        try {
            // Confirm the request exists, belongs to this user, and is approved
            $stmt = $this->db->prepare(
                "SELECT book_id FROM borrow_requests
                WHERE request_id = :id
                AND user_id    = :uid
                AND status     = 'approved'
                LIMIT 1"
            );
            $stmt->execute([':id' => $requestId, ':uid' => $userId]);
            $row = $stmt->fetch();

            if (!$row) {
                throw new \Exception('Request not found, not yours, or not approved.');
            }

            $this->db->prepare(
                "UPDATE borrow_requests
                SET status = 'returned', return_date = NOW()
                WHERE request_id = :id"
            )->execute([':id' => $requestId]);

            $this->db->prepare(
                "UPDATE books
                SET available_copies = available_copies + 1
                WHERE book_id = :bid"
            )->execute([':bid' => $row['book_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Borrow::returnRequest — ' . $e->getMessage());
            return false;
        }
    }
}