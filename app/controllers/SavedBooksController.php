<?php

require_once __DIR__ . '/../../core/Controller.php';

class SavedBooksController extends Controller {

    private $savedModel;

    public function __construct() {
        require_once __DIR__ . '/../models/Savedbooks.php';
        $this->savedModel = new Savedbooks();
    }

    // -----------------------------------------------------------------------
    // GET /mybooks  — main page with saved + borrowed sections
    // -----------------------------------------------------------------------
    public function index(): void {
        $this->requireLogin();

        $userId = (int) $_SESSION['user_id'];

        $savedBooks    = $this->savedModel->getSavedBooks($userId);
        $borrowedBooks = $this->savedModel->getBorrowedBooks($userId);

        $this->view('mybooks/index', [
            'pageTitle'     => 'My Books',
            'savedBooks'    => $savedBooks,
            'borrowedBooks' => $borrowedBooks,
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /saved/save  — AJAX: save a book
    // Expects JSON body or POST field: book_id
    // Returns JSON
    // -----------------------------------------------------------------------
    public function save(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $bookId = (int) ($_POST['book_id'] ?? 0);
        if ($bookId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid book ID.'], 400);
        }

        $userId  = (int) $_SESSION['user_id'];
        $success = $this->savedModel->save($userId, $bookId);

        $this->json([
            'success' => $success,
            'saved'   => true,
            'message' => $success ? 'Book saved.' : 'Could not save book.',
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /saved/unsave  — AJAX: remove a saved book
    // -----------------------------------------------------------------------
    public function unsave(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $bookId = (int) ($_POST['book_id'] ?? 0);
        if ($bookId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid book ID.'], 400);
        }

        $userId  = (int) $_SESSION['user_id'];
        $success = $this->savedModel->unsave($userId, $bookId);

        $this->json([
            'success' => $success,
            'saved'   => false,
            'message' => $success ? 'Book removed.' : 'Could not remove book.',
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /saved/status — AJAX: check if a book is saved by current user
    // -----------------------------------------------------------------------
    public function status(): void {
        $this->requireLogin();

        $bookId = (int) ($_POST['book_id'] ?? $_GET['book_id'] ?? 0);
        if ($bookId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid book ID.'], 400);
        }

        $userId = (int) $_SESSION['user_id'];
        $saved  = $this->savedModel->isSaved($userId, $bookId);

        $this->json(['success' => true, 'saved' => $saved]);
    }
}