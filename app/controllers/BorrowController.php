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

    // -----------------------------------------------------------------------
    // POST /borrow/return  — AJAX
    // User self-returns a book they currently have approved.
    // -----------------------------------------------------------------------
    public function return(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $requestId = (int) ($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid request ID.'], 400);
        }

        $userId  = (int) $_SESSION['user_id'];
        $success = $this->borrowModel->returnRequest($requestId, $userId);

        $this->json([
            'success' => $success,
            'message' => $success
                ? 'Book returned successfully.'
                : 'Could not process return. The request may not be active.',
        ]);
    }
}