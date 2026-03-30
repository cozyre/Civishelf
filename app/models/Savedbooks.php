<?php

class Savedbooks {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // -----------------------------------------------------------------------
    // Get all books saved by a user, newest first.
    // -----------------------------------------------------------------------
    public function getSavedBooks(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT b.book_id, b.book_title, b.cover_image, b.description,
                    b.available_copies, b.published_at,
                    a.author_name, c.category_name,
                    s.saved_at
             FROM saved_books s
             JOIN books       b ON s.book_id    = b.book_id
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             WHERE s.user_id = :user_id
             ORDER BY s.saved_at DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // Get all currently active borrows for a user (status = 'approved').
    // -----------------------------------------------------------------------
    public function getBorrowedBooks(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT b.book_id, b.book_title, b.cover_image, b.description,
                    b.available_copies, b.published_at,
                    a.author_name, c.category_name,
                    br.request_id, br.borrow_date, br.due_date, br.status
             FROM borrow_requests br
             JOIN books       b ON br.book_id    = b.book_id
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             WHERE br.user_id = :user_id
               AND br.status  = 'approved'
             ORDER BY br.due_date ASC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // Check if a user has already saved a book.
    // -----------------------------------------------------------------------
    public function isSaved(int $userId, int $bookId): bool {
        $stmt = $this->db->prepare(
            'SELECT save_id FROM saved_books
             WHERE user_id = :user_id AND book_id = :book_id
             LIMIT 1'
        );
        $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
        return (bool) $stmt->fetch();
    }

    // -----------------------------------------------------------------------
    // Save a book for a user. Silently ignores duplicates.
    // -----------------------------------------------------------------------
    public function save(int $userId, int $bookId): bool {
        if ($this->isSaved($userId, $bookId)) {
            return true; // already saved — not an error
        }

        $stmt = $this->db->prepare(
            'INSERT INTO saved_books (user_id, book_id, saved_at)
             VALUES (:user_id, :book_id, NOW())'
        );
        return $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
    }

    // -----------------------------------------------------------------------
    // Remove a saved book.
    // -----------------------------------------------------------------------
    public function unsave(int $userId, int $bookId): bool {
        $stmt = $this->db->prepare(
            'DELETE FROM saved_books
             WHERE user_id = :user_id AND book_id = :book_id'
        );
        return $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
    }
}