<?php
// core/Controller.php

class Controller {
    // -------------------------------------------------------
    // Model loader
    // Usage: $this->model('Book') → loads app/models/Book.php
    //        and returns a new instance.
    // -------------------------------------------------------
    protected function model(string $name): object {
        $file = __DIR__ . '/../app/models/' . $name . '.php';
        if (!file_exists($file)) {
            die('Model not found: ' . htmlspecialchars($name));
        }
        require_once $file;
        return new $name();
    }

    // -------------------------------------------------------
    // JSON response helper — use this for all AJAX endpoints
    // -------------------------------------------------------
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // -------------------------------------------------------
    // View loader — loads a PHP view file and passes data to it
    // Usage: $this->view('books/index', ['books' => $books])
    // -------------------------------------------------------
    protected function view(string $path, array $data = []): void {
        // Extract data array into variables accessible in the view
        extract($data);

        $file = __DIR__ . '/../app/views/' . $path . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            die('View not found: ' . htmlspecialchars($path));
        }

        include $file;
    }

    // -------------------------------------------------------
    // Redirect helper
    // -------------------------------------------------------
    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    // -------------------------------------------------------
    // Auth guards — call these at the top of controller methods
    // -------------------------------------------------------

    // Requires any logged-in user
    protected function requireLogin(): void {
        if (empty($_SESSION['user_id'])) {
            // If AJAX request, return JSON error instead of redirecting
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Unauthorized. Please log in.'], 401);
            }
            $this->redirect('login');
        }
    }

    // Requires admin role specifically
    protected function requireAdmin(): void {
        if (empty($_SESSION['admin_id'])) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Forbidden. Admins only.'], 403);
            }
            $this->redirect('admin/login');
        }
    }

    // -------------------------------------------------------
    // Detect AJAX requests (sent by jQuery $.ajax / $.post etc.)
    // -------------------------------------------------------
    protected function isAjax(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}