<?php

require_once __DIR__ . '/../../core/Controller.php';

class BorrowController extends Controller {

    private $borrowModel;

    public function __construct() {
        require_once __DIR__ . '/../models/Borrow.php';
        $this->borrowModel = new Borrow();
    }

    // -----------------------------------------------------------------------
    // POST /borrow/request  — AJAX
    // Submits a borrow request for the logged-in user.
    // -----------------------------------------------------------------------
    public function request(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $bookId = (int) ($_POST['book_id'] ?? 0);
        if ($bookId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid book ID.'], 400);
        }

        $userId = (int) $_SESSION['user_id'];

        // Prevent duplicate pending/approved requests
        if ($this->borrowModel->hasActiveBorrow($userId, $bookId)) {
            $this->json(['success' => false, 'message' => 'You already have an active request for this book.']);
            return;
        }

        $success = $this->borrowModel->createRequest($userId, $bookId);

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Borrow request submitted. Awaiting admin approval.'
                : 'Could not submit request. The book may be unavailable.',
        ]);
    }

    // POST /borrow/returnBook  — AJAX, user-initiated
    public function returnBook(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $requestId = (int) ($_POST['request_id'] ?? 0);
        if (!$requestId) {
            $this->json(['success' => false, 'message' => 'Invalid request ID.'], 400);
        }

        $userId  = (int) $_SESSION['user_id'];
        $success = $this->borrowModel->returnBook($requestId, $userId);

        $this->json([
            'success' => $success,
            'message' => $success ? 'Book returned successfully.' : 'Could not process return.',
        ]);
    }

    // GET /borrow/status?book_id=X  — AJAX, check pending status for current user
    public function status(): void {
        $this->requireLogin();

        $bookId = (int) ($_GET['book_id'] ?? $_POST['book_id'] ?? 0);
        if (!$bookId) {
            $this->json(['success' => false, 'message' => 'Invalid book ID.'], 400);
        }

        $userId  = (int) $_SESSION['user_id'];
        $pending = $this->borrowModel->hasPendingBorrow($userId, $bookId);

        $this->json(['success' => true, 'pending' => $pending]);
    }
}