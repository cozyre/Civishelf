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
    public function returnBook(int $requestId, int $userId): bool {
        $this->db->beginTransaction();
        try {
            // Verify ownership + status
            $stmt = $this->db->prepare(
                "SELECT book_id FROM borrow_requests
                WHERE request_id = :rid
                AND user_id   = :uid
                AND status    = 'approved'
                LIMIT 1"
            );
            $stmt->execute([':rid' => $requestId, ':uid' => $userId]);
            $row = $stmt->fetch();
            if (!$row) throw new \Exception('Not found or not yours');

            $this->db->prepare(
                "UPDATE borrow_requests SET status = 'returned', return_date = NOW()
                WHERE request_id = :rid"
            )->execute([':rid' => $requestId]);

            $this->db->prepare(
                "UPDATE books SET available_copies = available_copies + 1
                WHERE book_id = :bid"
            )->execute([':bid' => $row['book_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Borrow::returnBook: ' . $e->getMessage());
            return false;
        }
    }

    public function hasPendingBorrow(int $userId, int $bookId): bool {
        $stmt = $this->db->prepare(
            "SELECT request_id FROM borrow_requests
            WHERE user_id = :uid AND book_id = :bid AND status = 'pending'
            LIMIT 1"
        );
        $stmt->execute([':uid' => $userId, ':bid' => $bookId]);
        return (bool) $stmt->fetch();
    }

    public function getUserHistory(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT br.*, b.book_title, b.cover_image, a.author_name
            FROM borrow_requests br
            JOIN books b ON br.book_id = b.book_id
            LEFT JOIN authors a ON b.author_id = a.author_id
            WHERE br.user_id = :uid
            ORDER BY br.borrow_date DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
}