<?php

require_once __DIR__ . '/../../core/Controller.php';

class BookController extends Controller {

    public function index(): void {
        $bookModel     = $this->model('Book');
        $categoryModel = $this->model('Category');

        // Current logged-in user ID — null for guests
        $userId = $_SESSION['user_id'] ?? null;

        // Masonry hero — 6 most recent books
        $featuredBooks = $bookModel->getFeatured(6);

        // Most Popular carousel — ordered by borrow count
        $popularBooks = $bookModel->getPopularByBorrows(8);

        // Category filter
        $categories       = $categoryModel->getAll();
        $activeCategoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;

        // Main paginated grid
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $limit  = 8;
        $offset = ($page - 1) * $limit;
        $books  = $bookModel->getAll($limit, $offset, $activeCategoryId);

        // Note: this runs one extra query per book. For a campus project this
        // is fine. If it becomes slow later, replace with a single JOIN query.
        
        $resolveStatus = function (array &$bookList) use ($bookModel, $userId): void {
            foreach ($bookList as &$book) {
                $resolved           = $bookModel->resolveUserStatus($book['book_id'], $userId);
                $book['status']     = $resolved['status'];
                $book['due_date']   = $resolved['due_date'];
            }
            unset($book); // break reference
        };

        $resolveStatus($featuredBooks);
        $resolveStatus($popularBooks);
        $resolveStatus($books);

        $this->view('books/index', [
            'pageTitle'        => 'Explore',
            'featuredBooks'    => $featuredBooks,
            'popularBooks'     => $popularBooks,
            'books'            => $books,
            'categories'       => $categories,
            'activeCategoryId' => $activeCategoryId,
            'currentPage'      => $page,
        ]);
    }

    // -----------------------------------------------------------------------
    // GET /books/offline  — Not available online info page
    // Shown when a user tries to read a borrowed book that has no online file.
    // -----------------------------------------------------------------------
    public function offline(): void {
    $this->view('books/offline', ['pageTitle' => 'Not Available Online']);
}
}