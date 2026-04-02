<?php

class AdminController extends Controller {

    private $userModel;
    private $adminModel;

    public function __construct() {
        $this->userModel  = $this->model('User');
        $this->adminModel = $this->model('Admin');
    }

    // ========================================================================
    // AUTH
    // ========================================================================

    public function login(): void {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/administrator');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAdminLogin();
            return;
        }

        $this->view('auth/admin_login', ['pageTitle' => 'Admin Login']);
    }

    private function handleAdminLogin(): void {
        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

        $errors = [];
        if (empty($email) || empty($password)) {
            $errors[] = 'All fields are required.';
        }

        if (empty($errors)) {
            $admin = $this->userModel->findAdminByEmail($email);
            if (!$admin || !$this->userModel->verifyPassword($password, $admin['password_hash'])) {
                $errors[] = 'Invalid credentials or insufficient privileges.';
            }
        }

        if (!empty($errors)) {
            $this->view('auth/admin_login', [
                'errors'    => $errors,
                'email'     => $email ?? '',
                'pageTitle' => 'Admin Login',
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id']   = $admin['user_id'];
        $_SESSION['admin_name'] = $admin['user_name'];

        flash('success', 'Welcome, ' . htmlspecialchars($admin['user_name']) . '.');
        $this->redirect('/administrator');
    }

    public function logout(): void {
        unset($_SESSION['admin_id'], $_SESSION['admin_name']);
        session_regenerate_id(true);
        flash('success', 'Admin session ended.');
        $this->redirect('/admin/login');
    }

    // ========================================================================
    // DASHBOARD  GET /administrator
    // ========================================================================

    public function index(): void {
        $this->requireAdmin();

        $this->view('administrator/index', [
            'pageTitle'      => 'Dashboard',
            'activeNav'      => 'dashboard',
            'totalUsers'     => $this->adminModel->getTotalUsers(),
            'totalBooks'     => $this->adminModel->getTotalBooks(),
            'activeLoans'    => $this->adminModel->getActiveLoans(),
            'pendingReqs'    => $this->adminModel->getPendingRequests(),
            'overdueCount'   => $this->adminModel->getOverdueCount(),
            'recentActivity' => $this->adminModel->getRecentActivity(10),
        ]);
    }

    // ========================================================================
    // USERS  GET /administrator/users
    // ========================================================================

    public function users(): void {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $this->view('administrator/users', [
            'pageTitle'  => 'User Management',
            'activeNav'  => 'users',
            'users'      => $this->adminModel->getAllUsers($search, $limit, $offset),
            'totalUsers' => $this->adminModel->countUsers($search),
            'search'     => $search,
            'page'       => $page,
            'limit'      => $limit,
        ]);
    }

    // POST /administrator/user/status  (AJAX)
    public function userStatus(): void {
        $this->requireAdmin();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$userId || !in_array($status, ['active', 'banned'], true)) {
            $this->json(['success' => false, 'message' => 'Invalid input.'], 400);
        }

        $ok = $this->adminModel->setUserStatus($userId, $status);
        $this->json(['success' => $ok, 'message' => $ok ? 'Status updated.' : 'Failed.']);
    }

    // POST /administrator/user/delete  (AJAX)
    public function userDelete(): void {
        $this->requireAdmin();

        $userId = (int) ($_POST['user_id'] ?? 0);
        if (!$userId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->deleteUser($userId);
        $this->json(['success' => $ok, 'message' => $ok ? 'User deleted.' : 'Failed.']);
    }

    // GET /administrator/user/history?user_id=X  (AJAX)
    public function userHistory(): void {
        $this->requireAdmin();

        $userId = (int) ($_GET['user_id'] ?? 0);
        if (!$userId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $history = $this->adminModel->getUserBorrowHistory($userId);
        $this->json(['success' => true, 'history' => $history]);
    }

    // ========================================================================
    // BOOKS  GET /administrator/books
    // ========================================================================

    public function books(): void {
        $this->requireAdmin();

        $search     = trim($_GET['search'] ?? '');
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $limit      = 15;
        $offset     = ($page - 1) * $limit;

        $this->view('administrator/books', [
            'pageTitle'        => 'Book Management',
            'activeNav'        => 'books',
            'books'            => $this->adminModel->getAllBooks($search, $categoryId, $limit, $offset),
            'totalBooks'       => $this->adminModel->countBooks($search, $categoryId),
            'categories'       => $this->adminModel->getAllCategories(),
            'authors'          => $this->adminModel->getAllAuthors(),
            'search'           => $search,
            'activeCategoryId' => $categoryId,
            'page'             => $page,
            'limit'            => $limit,
        ]);
    }

    // POST /administrator/book/save  (create or update)
    public function bookSave(): void {
        $this->requireAdmin();

        $bookId = (int) ($_POST['book_id'] ?? 0);

        $data = [
            'book_title'   => trim($_POST['book_title']    ?? ''),
            'author_id'    => (int) ($_POST['author_id']   ?? 0),
            'category_id'  => (int) ($_POST['category_id'] ?? 0),
            'description'  => trim($_POST['description']   ?? ''),
            'total_copies' => (int) ($_POST['total_copies'] ?? 1),
            'published_at' => $_POST['published_at'] ?? date('Y-m-d'),
            'is_online'    => isset($_POST['is_online']) ? 1 : 0,
            'cover_image'  => trim($_POST['existing_cover'] ?? ''),
            'file_path'    => trim($_POST['existing_file']  ?? ''),
        ];

        if (!empty($_FILES['cover_image']['name'])) {
            $upload = $this->handleUpload('cover_image', 'covers', ['jpg','jpeg','png','webp']);
            if ($upload['ok']) {
                $data['cover_image'] = $upload['filename'];
            } else {
                flash('danger', 'Cover upload failed: ' . $upload['error']);
                $this->redirect('/administrator/books');
            }
        }

        if (!empty($_FILES['book_file']['name'])) {
            $upload = $this->handleUpload('book_file', 'books', ['pdf']);
            if ($upload['ok']) {
                $data['file_path'] = $upload['filename'];
            } else {
                flash('danger', 'PDF upload failed: ' . $upload['error']);
                $this->redirect('/administrator/books');
            }
        }

        if ($bookId > 0) {
            $ok = $this->adminModel->updateBook($bookId, $data);
            flash($ok ? 'success' : 'danger', $ok ? 'Book updated.' : 'Update failed.');
        } else {
            $ok = $this->adminModel->createBook($data);
            flash($ok ? 'success' : 'danger', $ok ? 'Book added.' : 'Add failed.');
        }

        $this->redirect('/administrator/books');
    }

    // POST /administrator/book/delete  (AJAX)
    public function bookDelete(): void {
        $this->requireAdmin();

        $bookId = (int) ($_POST['book_id'] ?? 0);
        if (!$bookId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->deleteBook($bookId);
        $this->json(['success' => $ok, 'message' => $ok ? 'Book deleted.' : 'Failed.']);
    }

    // POST /administrator/category/save
    public function categorySave(): void {
        $this->requireAdmin();

        $name = trim($_POST['category_name'] ?? '');
        $desc = trim($_POST['description']   ?? '');

        if (strlen($name) < 2) {
            flash('danger', 'Category name must be at least 2 characters.');
            $this->redirect('/administrator/books');
        }

        $ok = $this->adminModel->createCategory($name, $desc);
        flash($ok ? 'success' : 'danger', $ok ? 'Category created.' : 'Failed.');
        $this->redirect('/administrator/books');
    }

    // POST /administrator/category/delete  (AJAX)
    public function categoryDelete(): void {
        $this->requireAdmin();

        $id = (int) ($_POST['category_id'] ?? 0);
        if (!$id) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->deleteCategory($id);
        $this->json(['success' => $ok, 'message' => $ok ? 'Category deleted.' : 'Failed.']);
    }

    // POST /administrator/author/save
    public function authorSave(): void {
        $this->requireAdmin();

        $name = trim($_POST['author_name'] ?? '');
        $bio  = trim($_POST['bio']         ?? '');

        if (strlen($name) < 2) {
            flash('danger', 'Author name must be at least 2 characters.');
            $this->redirect('/administrator/books');
        }

        $ok = $this->adminModel->createAuthor($name, $bio);
        flash($ok ? 'success' : 'danger', $ok ? 'Author created.' : 'Failed.');
        $this->redirect('/administrator/books');
    }

    // ========================================================================
    // BORROWS  GET /administrator/borrows
    // ========================================================================

    public function borrows(): void {
        $this->requireAdmin();

        $statusFilter = $_GET['status'] ?? '';
        $page         = max(1, (int) ($_GET['page'] ?? 1));
        $limit        = 20;
        $offset       = ($page - 1) * $limit;

        $this->view('administrator/borrows', [
            'pageTitle'    => 'Borrow Approval',
            'activeNav'    => 'borrows',
            'borrows'      => $this->adminModel->getBorrowRequests($statusFilter, $limit, $offset),
            'totalBorrows' => $this->adminModel->countBorrowRequests($statusFilter),
            'statusFilter' => $statusFilter,
            'page'         => $page,
            'limit'        => $limit,
            'pendingCount' => $this->adminModel->getPendingRequests(),
            'overdueCount' => $this->adminModel->getOverdueCount(),
        ]);
    }

    // POST /administrator/borrow/approve  (AJAX)
    public function borrowApprove(): void {
        $this->requireAdmin();

        $requestId = (int) ($_POST['request_id'] ?? 0);
        $dueDate   = $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days'));

        if (!$requestId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->approveBorrow($requestId, $dueDate);
        $this->json(['success' => $ok, 'message' => $ok ? 'Request approved.' : 'Approval failed.']);
    }

    // POST /administrator/borrow/reject  (AJAX)
    public function borrowReject(): void {
        $this->requireAdmin();

        $requestId = (int) ($_POST['request_id'] ?? 0);
        if (!$requestId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->rejectBorrow($requestId);
        $this->json(['success' => $ok, 'message' => $ok ? 'Request rejected.' : 'Failed.']);
    }

    // POST /administrator/borrow/return  (AJAX)
    public function borrowReturn(): void {
        $this->requireAdmin();

        $requestId = (int) ($_POST['request_id'] ?? 0);
        if (!$requestId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->returnBorrow($requestId);
        $this->json(['success' => $ok, 'message' => $ok ? 'Marked as returned.' : 'Failed.']);
    }

    // ========================================================================
    // NEWS  GET /administrator/news
    // ========================================================================

    public function news(): void {
        $this->requireAdmin();

        $this->view('administrator/news', [
            'pageTitle' => 'News Management',
            'activeNav' => 'news',
            'newsList'  => $this->adminModel->getAllNews(20, 0),
        ]);
    }

    // POST /administrator/news/save
    public function newsSave(): void {
        $this->requireAdmin();

        $newsId  = (int) ($_POST['news_id']    ?? 0);
        $title   = trim($_POST['news_title']   ?? '');
        $content = trim($_POST['content']      ?? '');
        $image   = trim($_POST['existing_image'] ?? '');

        if (strlen($title) < 3 || strlen($content) < 10) {
            flash('danger', 'Title and content are required.');
            $this->redirect('/administrator/news');
        }

        if (!empty($_FILES['news_image']['name'])) {
            $upload = $this->handleUpload('news_image', 'news', ['jpg','jpeg','png','webp']);
            if ($upload['ok']) {
                $image = $upload['filename'];
            } else {
                flash('danger', 'Image upload failed: ' . $upload['error']);
                $this->redirect('/administrator/news');
            }
        }

        if ($newsId > 0) {
            $ok = $this->adminModel->updateNews($newsId, $title, $content, $image);
            flash($ok ? 'success' : 'danger', $ok ? 'Article updated.' : 'Update failed.');
        } else {
            $ok = $this->adminModel->createNews($title, $content, $image);
            flash($ok ? 'success' : 'danger', $ok ? 'Article published.' : 'Publish failed.');
        }

        $this->redirect('/administrator/news');
    }

    // POST /administrator/news/delete  (AJAX)
    public function newsDelete(): void {
        $this->requireAdmin();

        $newsId = (int) ($_POST['news_id'] ?? 0);
        if (!$newsId) $this->json(['success' => false, 'message' => 'Invalid ID.'], 400);

        $ok = $this->adminModel->deleteNews($newsId);
        $this->json(['success' => $ok, 'message' => $ok ? 'Article deleted.' : 'Failed.']);
    }

    // ========================================================================
    // PRIVATE: file upload helper
    // Covers/news images → public/assets/images/{subdir}/
    // PDFs              → storage/books/  (outside public root)
    // ========================================================================

    private function handleUpload(string $inputName, string $subdir, array $allowed): array {
        $file = $_FILES[$inputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'filename' => '', 'error' => 'Upload error ' . $file['error']];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return ['ok' => false, 'filename' => '', 'error' => "Type .{$ext} not allowed."];
        }

        if ($file['size'] > 20 * 1024 * 1024) {
            return ['ok' => false, 'filename' => '', 'error' => 'File exceeds 20 MB limit.'];
        }

        $filename = uniqid('', true) . '.' . $ext;

        $dest = ($ext === 'pdf')
            ? __DIR__ . '/../../storage/' . $subdir . '/' . $filename
            : __DIR__ . '/../../public/assets/images/' . $subdir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Could not save file.'];
        }

        return ['ok' => true, 'filename' => $filename, 'error' => ''];
    }
}