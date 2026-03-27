<?php

class Book {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // -----------------------------------------------------------------------
    // Most popular books by borrow request count.
    // Used by: BookController (Explore page hero carousel)
    // -----------------------------------------------------------------------
    public function getPopularByBorrows(int $limit = 8): array {
        $stmt = $this->db->prepare(
            'SELECT b.*, a.author_name, c.category_name,
                    COUNT(br.request_id) AS borrow_count
             FROM books b
             LEFT JOIN authors    a  ON b.author_id    = a.author_id
             LEFT JOIN categories c  ON b.category_id  = c.category_id
             LEFT JOIN borrow_requests br ON b.book_id = br.book_id
             GROUP BY b.book_id
             ORDER BY borrow_count DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // Most popular books by save count.
    // Used by: HomeController (Home page top-searched carousel)
    // -----------------------------------------------------------------------
    public function getPopularBySaves(int $limit = 10): array {
        $stmt = $this->db->prepare(
            'SELECT b.*, a.author_name, c.category_name,
                    COUNT(s.save_id) AS save_count
             FROM books b
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             LEFT JOIN saved_books s ON b.book_id    = s.book_id
             GROUP BY b.book_id
             ORDER BY save_count DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // Paginated book listing, optionally filtered by category.
    // Used by: HomeController (Major Needs grid), BookController (main grid)
    // -----------------------------------------------------------------------
    public function getAll(int $limit = 8, int $offset = 0, ?int $categoryId = null): array {
        $where = $categoryId ? 'WHERE b.category_id = :category_id' : '';

        $stmt = $this->db->prepare(
            "SELECT b.*, a.author_name, c.category_name
             FROM books b
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             {$where}
             ORDER BY b.published_at DESC
             LIMIT :limit OFFSET :offset"
        );

        if ($categoryId) {
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // A small set for the masonry hero on the Explore page.
    // Picks the most recently added books — tweak ORDER BY as needed.
    // -----------------------------------------------------------------------
    public function getFeatured(int $limit = 6): array {
        $stmt = $this->db->prepare(
            'SELECT b.*, a.author_name, c.category_name
             FROM books b
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             ORDER BY b.published_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // -----------------------------------------------------------------------
    // Resolve the current user's relationship to a specific book.
    // Returns: 'borrowed' | 'online' | 'none'
    //
    // Logic:
    //   1. If the book has is_online = 1, status is 'online' (readable for all logged-in users).
    //   2. If the user has an approved/unreturned borrow request, status is 'borrowed'.
    //   3. Otherwise 'none'.
    //
    // Used by: BookController to build the data-status attribute on each card.
    // -----------------------------------------------------------------------
    public function resolveUserStatus(int $bookId, ?int $userId): array {
        // Default for guests
        if (!$userId) {
            return ['status' => 'none', 'due_date' => null];
        }

        // Check is_online first
        $stmt = $this->db->prepare(
            'SELECT is_online FROM books WHERE book_id = :book_id LIMIT 1'
        );
        $stmt->execute([':book_id' => $bookId]);
        $book = $stmt->fetch();

        if ($book && $book['is_online']) {
            return ['status' => 'online', 'due_date' => null];
        }

        // Check active borrow
        $stmt = $this->db->prepare(
            "SELECT due_date FROM borrow_requests
             WHERE book_id = :book_id
               AND user_id = :user_id
               AND status  = 'approved'
             ORDER BY borrow_date DESC
             LIMIT 1"
        );
        $stmt->execute([':book_id' => $bookId, ':user_id' => $userId]);
        $borrow = $stmt->fetch();

        if ($borrow) {
            return ['status' => 'borrowed', 'due_date' => $borrow['due_date']];
        }

        return ['status' => 'none', 'due_date' => null];
    }
}
