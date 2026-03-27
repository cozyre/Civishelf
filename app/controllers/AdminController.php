<?php

class AdminController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // -----------------------------------------------------------------------
    // GET /administrator  (main dashboard)
    // -----------------------------------------------------------------------
    public function index() {
        $this->requireAdmin();
        $this->view('administrator/index', ['pageTitle' => 'Admin Dashboard']);
    }

    // -----------------------------------------------------------------------
    // GET/POST /admin/login
    // -----------------------------------------------------------------------
    public function login() {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/administrator');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAdminLogin();
            return;
        }

        $this->view('auth/admin_login', ['pageTitle' => 'Admin Login']);
    }

    private function handleAdminLogin() {
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
                'email'     => $email,
                'pageTitle' => 'Admin Login'
            ]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION['admin_id']   = $admin['user_id'];
        $_SESSION['admin_name'] = $admin['user_name'];

        flash('success', 'Welcome, ' . htmlspecialchars($admin['user_name']) . '.');
        $this->redirect('/administrator');
    }

    // -----------------------------------------------------------------------
    // GET /admin/logout
    // -----------------------------------------------------------------------
    public function logout() {
        unset($_SESSION['admin_id'], $_SESSION['admin_name']);
        session_regenerate_id(true);
        flash('success', 'Admin session ended.');
        $this->redirect('/admin/login');
    }
}