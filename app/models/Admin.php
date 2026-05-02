<?php

class Admin {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ========================================================================
    // DASHBOARD STATS
    // ========================================================================

    public function getTotalUsers(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    }

    public function getTotalBooks(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM books")->fetchColumn();
    }

    public function getActiveLoans(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM borrow_requests WHERE status = 'approved'")->fetchColumn();
    }

    public function getPendingRequests(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM borrow_requests WHERE status = 'pending'")->fetchColumn();
    }

    public function getOverdueCount(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM borrow_requests WHERE status = 'approved' AND due_date < NOW()"
        )->fetchColumn();
    }

    // ========================================================================
    // RECENT ACTIVITY (dashboard feed)
    // ========================================================================

    public function getRecentActivity(int $limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT br.request_id, br.status, br.borrow_date, br.due_date, br.return_date,
                    u.user_name, u.email,
                    b.book_title
             FROM borrow_requests br
             JOIN users u ON br.user_id = u.user_id
             JOIN books b ON br.book_id = b.book_id
             ORDER BY br.borrow_date DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ========================================================================
    // USER MANAGEMENT
    // ========================================================================

    public function getAllUsers(string $search = '', int $limit = 20, int $offset = 0): array {
        $where = $search
            ? "WHERE u.role = 'user' AND (u.user_name LIKE :q OR u.email LIKE :q)"
            : "WHERE u.role = 'user'";

        $stmt = $this->db->prepare(
            "SELECT u.*,
                    COUNT(DISTINCT br.request_id) AS total_borrows,
                    COUNT(DISTINCT CASE WHEN br.status = 'approved' THEN br.request_id END) AS active_borrows
             FROM users u
             LEFT JOIN borrow_requests br ON u.user_id = br.user_id
             {$where}
             GROUP BY u.user_id
             ORDER BY u.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        if ($search) $stmt->bindValue(':q', '%' . $search . '%');
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUsers(string $search = ''): int {
        $where = $search
            ? "WHERE role = 'user' AND (user_name LIKE :q OR email LIKE :q)"
            : "WHERE role = 'user'";
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users {$where}");
        if ($search) $stmt->bindValue(':q', '%' . $search . '%');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function setUserStatus(int $userId, string $status): bool {
        $stmt = $this->db->prepare(
            "UPDATE users SET user_status = :status WHERE user_id = :id AND role = 'user'"
        );
        return $stmt->execute([':status' => $status, ':id' => $userId]);
    }

    public function deleteUser(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id AND role = 'user'");
        return $stmt->execute([':id' => $userId]);
    }

    public function getUserBorrowHistory(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT br.*, b.book_title, b.cover_image
             FROM borrow_requests br
             JOIN books b ON br.book_id = b.book_id
             WHERE br.user_id = :uid
             ORDER BY br.borrow_date DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    // ========================================================================
    // BOOK MANAGEMENT
    // ========================================================================

    public function getAllBooks(string $search = '', ?int $categoryId = null, int $limit = 20, int $offset = 0): array {
        $conditions = [];
        if ($search)     $conditions[] = "(b.book_title LIKE :q OR a.author_name LIKE :q)";
        if ($categoryId) $conditions[] = "b.category_id = :cat";
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $stmt = $this->db->prepare(
            "SELECT b.*, a.author_name, c.category_name
             FROM books b
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             {$where}
             ORDER BY b.book_id DESC
             LIMIT :limit OFFSET :offset"
        );
        if ($search)     $stmt->bindValue(':q',   '%' . $search . '%');
        if ($categoryId) $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countBooks(string $search = '', ?int $categoryId = null): int {
        $conditions = [];
        if ($search)     $conditions[] = "(b.book_title LIKE :q OR a.author_name LIKE :q)";
        if ($categoryId) $conditions[] = "b.category_id = :cat";
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM books b
             LEFT JOIN authors a ON b.author_id = a.author_id
             {$where}"
        );
        if ($search)     $stmt->bindValue(':q',   '%' . $search . '%');
        if ($categoryId) $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getBookById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT b.*, a.author_name, c.category_name
             FROM books b
             LEFT JOIN authors    a ON b.author_id   = a.author_id
             LEFT JOIN categories c ON b.category_id = c.category_id
             WHERE b.book_id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function createBook(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO books
                (book_title, author_id, category_id, description, cover_image,
                 file_path, total_copies, available_copies, published_at, is_online)
             VALUES
                (:title, :author, :category, :desc, :cover,
                 :file, :total, :total, :published, :online)"
        );
        return $stmt->execute([
            ':title'     => $data['book_title'],
            ':author'    => $data['author_id'],
            ':category'  => $data['category_id'],
            ':desc'      => $data['description'],
            ':cover'     => $data['cover_image'],
            ':file'      => $data['file_path'] ?? null,
            ':total'     => (int) $data['total_copies'],
            ':published' => $data['published_at'],
            ':online'    => (int) ($data['is_online'] ?? 0),
        ]);
    }

    public function updateBook(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE books SET
                book_title   = :title,
                author_id    = :author,
                category_id  = :category,
                description  = :desc,
                cover_image  = :cover,
                total_copies = :total,
                published_at = :published,
                is_online    = :online
             WHERE book_id = :id"
        );
        return $stmt->execute([
            ':title'     => $data['book_title'],
            ':author'    => (int) $data['author_id'],
            ':category'  => (int) $data['category_id'],
            ':desc'      => $data['description'],
            ':cover'     => $data['cover_image'],
            ':total'     => (int) $data['total_copies'],
            ':published' => $data['published_at'],
            ':online'    => (int) ($data['is_online'] ?? 0),
            ':id'        => $id,
        ]);
    }

    public function deleteBook(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM books WHERE book_id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ========================================================================
    // AUTHORS & CATEGORIES
    // ========================================================================

    public function getAllAuthors(): array {
        return $this->db->query(
            "SELECT author_id, author_name FROM authors ORDER BY author_name ASC"
        )->fetchAll();
    }

    public function getAllCategories(): array {
        return $this->db->query(
            "SELECT category_id, category_name FROM categories ORDER BY category_name ASC"
        )->fetchAll();
    }

    public function createCategory(string $name, string $desc): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO categories (category_name, description) VALUES (:name, :desc)"
        );
        return $stmt->execute([':name' => $name, ':desc' => $desc]);
    }

    public function deleteCategory(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE category_id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function createAuthor(string $name, string $bio): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO authors (author_name, bio) VALUES (:name, :bio)"
        );
        return $stmt->execute([':name' => $name, ':bio' => $bio]);
    }

    // ========================================================================
    // BORROW MANAGEMENT
    // ========================================================================

    public function getBorrowRequests(string $statusFilter = '', int $limit = 30, int $offset = 0): array {
        $where = $statusFilter ? "WHERE br.status = :status" : '';
        $stmt  = $this->db->prepare(
            "SELECT br.*, u.user_name, u.email, b.book_title, b.cover_image
             FROM borrow_requests br
             JOIN users u ON br.user_id = u.user_id
             JOIN books b ON br.book_id = b.book_id
             {$where}
             ORDER BY FIELD(br.status, 'pending', 'approved', 'returned', 'rejected'), br.borrow_date DESC
             LIMIT :limit OFFSET :offset"
        );
        if ($statusFilter) $stmt->bindValue(':status', $statusFilter);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countBorrowRequests(string $statusFilter = ''): int {
        $where = $statusFilter ? "WHERE status = :status" : '';
        $stmt  = $this->db->prepare("SELECT COUNT(*) FROM borrow_requests {$where}");
        if ($statusFilter) $stmt->bindValue(':status', $statusFilter);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Approve: set status, update due date, decrement available_copies.
     * Wrapped in a transaction so inventory stays consistent.
     */
    public function approveBorrow(int $requestId, string $dueDate): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT book_id FROM borrow_requests WHERE request_id = :id AND status = 'pending'"
            );
            $stmt->execute([':id' => $requestId]);
            $row = $stmt->fetch();
            if (!$row) throw new \Exception('Request not found or not pending');

            $this->db->prepare(
                "UPDATE borrow_requests SET status = 'approved', due_date = :due WHERE request_id = :id"
            )->execute([':due' => $dueDate, ':id' => $requestId]);

            $this->db->prepare(
                "UPDATE books SET available_copies = GREATEST(available_copies - 1, 0) WHERE book_id = :bid"
            )->execute([':bid' => $row['book_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('approveBorrow: ' . $e->getMessage());
            return false;
        }
    }

    public function rejectBorrow(int $requestId): bool {
        $stmt = $this->db->prepare(
            "UPDATE borrow_requests SET status = 'rejected' WHERE request_id = :id AND status = 'pending'"
        );
        return $stmt->execute([':id' => $requestId]);
    }

    public function returnBorrow(int $requestId): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT book_id FROM borrow_requests WHERE request_id = :id AND status = 'approved'"
            );
            $stmt->execute([':id' => $requestId]);
            $row = $stmt->fetch();
            if (!$row) throw new \Exception('Request not found or not approved');

            $this->db->prepare(
                "UPDATE borrow_requests SET status = 'returned', return_date = NOW() WHERE request_id = :id"
            )->execute([':id' => $requestId]);

            $this->db->prepare(
                "UPDATE books SET available_copies = available_copies + 1 WHERE book_id = :bid"
            )->execute([':bid' => $row['book_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('returnBorrow: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // NEWS MANAGEMENT
    // ========================================================================

    public function getAllNews(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM news ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNewsById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE news_id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function createNews(string $title, string $content, string $image): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO news (news_title, content, image, created_at) VALUES (:title, :content, :image, NOW())"
        );
        return $stmt->execute([':title' => $title, ':content' => $content, ':image' => $image]);
    }

    public function updateNews(int $id, string $title, string $content, string $image): bool {
        $stmt = $this->db->prepare(
            "UPDATE news SET news_title = :title, content = :content, image = :image WHERE news_id = :id"
        );
        return $stmt->execute([
            ':title'   => $title,
            ':content' => $content,
            ':image'   => $image,
            ':id'      => $id,
        ]);
    }

    public function deleteNews(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM news WHERE news_id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ========================================================================
    // CONTACT MESSAGES
    // ========================================================================

    public function getContactMessages(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countContactMessages(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    }

    public function deleteContactMessage(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE message_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}