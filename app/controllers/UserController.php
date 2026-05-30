<?php

class UserController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // -----------------------------------------------------------------------
    // POST /user/login
    // -----------------------------------------------------------------------
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

        if (empty($email) || empty($password)) {
            $this->failLogin('All fields are required.', $email);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->failLogin('Invalid email or password.', $email);
            return;
        }

        if ($user['user_status'] === 'banned') {
            $this->failLogin('Your account has been suspended. Contact support.', $email);
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_role'] = $user['role'];

        // If admin, also open the admin session immediately
        if ($user['role'] === 'admin') {
            $_SESSION['admin_id']   = $user['user_id'];
            $_SESSION['admin_name'] = $user['user_name'];
        }

        flash('success', 'Welcome back, ' . htmlspecialchars($user['user_name']) . '!');
        $this->redirect('/');
    }

    // -----------------------------------------------------------------------
    // GET /user/register -> show form
    // POST /user/register -> process
    // -----------------------------------------------------------------------
    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('../');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
            return;
        }

        $this->view('auth/register', ['pageTitle' => 'Register']);
    }

    private function handleRegister() {
        $name     = trim(filter_input(INPUT_POST, 'name',             FILTER_SANITIZE_SPECIAL_CHARS));
        $email    = trim(filter_input(INPUT_POST, 'email',            FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password',         FILTER_DEFAULT));
        $confirm  = trim(filter_input(INPUT_POST, 'confirm_password', FILTER_DEFAULT));

        $errors = [];

        if (strlen($name) < 2)                          $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (strlen($password) < 8)                      $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)                     $errors[] = 'Passwords do not match.';

        if (empty($errors) && $this->userModel->emailExists($email)) {
            $errors[] = 'That email is already registered.';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'errors'    => $errors,
                'name'      => $name,
                'email'     => $email,
                'pageTitle' => 'Register',
            ]);
            return;
        }

        $newId = $this->userModel->create($name, $email, $password);

        if (!$newId) {
            $this->view('auth/register', [
                'errors'    => ['Registration failed. Please try again.'],
                'pageTitle' => 'Register',
            ]);
            return;
        }

        flash('success', 'Account created! You can now log in.');
        $this->redirect('/');
    }

    // -----------------------------------------------------------------------
    // GET /user/logout
    // -----------------------------------------------------------------------
    public function logout(): void {
        $this->redirect('/admin/logout');
    }

    // -----------------------------------------------------------------------
    // Private: failed login redirect
    // -----------------------------------------------------------------------
    private function failLogin(string $message, string $email = ''): void {
        $_SESSION['login_failed']  = true;
        $_SESSION['login_error']   = $message;
        $_SESSION['login_prefill'] = $email;

        $back = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/';
        header('Location: ' . $back);
        exit;
    }

    public function profile(): void {
        $this->requireLogin();

        $userId = (int) $_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            flash('danger', 'User not found.');
            $this->redirect('/');
        }

        $totalBorrows = $this->userModel->getTotalBorrows($userId);

        $this->view('user/profile', [
            'pageTitle'    => 'My Account',
            'user'         => $user,
            'totalBorrows' => $totalBorrows,
        ]);
    }

    public function history(): void {
        $this->requireLogin();

        require_once __DIR__ . '/../models/Borrow.php';
        $borrowModel = new Borrow();

        $history = $borrowModel->getUserHistory((int) $_SESSION['user_id']);

        $this->view('user/history', [
            'pageTitle' => 'Borrow History',
            'history'   => $history,
        ]);
    }

    // POST /user/updateProfile - AJAX
    public function updateProfile(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $userId = (int) $_SESSION['user_id'];
        $name   = trim(filter_input(INPUT_POST, 'name',  FILTER_SANITIZE_SPECIAL_CHARS));
        $email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

        if (strlen($name) < 2) {
            $this->json(['success' => false, 'message' => 'Name must be at least 2 characters.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'Please enter a valid email address.']);
        }
        if ($this->userModel->emailExistsForOther($email, $userId)) {
            $this->json(['success' => false, 'message' => 'That email is already in use by another account.']);
        }

        $ok = $this->userModel->updateProfile($userId, $name, $email);

        if ($ok) {
            // Sync session so navbar updates without a reload
            $_SESSION['user_name'] = $name;
            if (isset($_SESSION['admin_name'])) {
                $_SESSION['admin_name'] = $name;
            }
        }

        $this->json([
            'success' => $ok,
            'message' => $ok ? 'Profile updated.' : 'Update failed.',
            'name'    => $name,
        ]);
    }

    // POST /user/changePassword - AJAX
    public function changePassword(): void {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method.'], 405);
        }

        $userId  = (int) $_SESSION['user_id'];
        $current = trim(filter_input(INPUT_POST, 'current_password', FILTER_DEFAULT));
        $new     = trim(filter_input(INPUT_POST, 'new_password',     FILTER_DEFAULT));
        $confirm = trim(filter_input(INPUT_POST, 'confirm_password', FILTER_DEFAULT));

        if (empty($current) || empty($new) || empty($confirm)) {
            $this->json(['success' => false, 'message' => 'All fields are required.']);
        }
        if (strlen($new) < 8) {
            $this->json(['success' => false, 'message' => 'New password must be at least 8 characters.']);
        }
        if ($new !== $confirm) {
            $this->json(['success' => false, 'message' => 'New passwords do not match.']);
        }

        // Re-fetch user to verify current password against DB
        $user = $this->userModel->findById($userId);
        if (!$user || !$this->userModel->verifyPassword($current, $user['password_hash'])) {
            $this->json(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        $ok = $this->userModel->updatePassword($userId, password_hash($new, PASSWORD_DEFAULT));

        $this->json([
            'success' => $ok,
            'message' => $ok ? 'Password changed successfully.' : 'Failed to update password.',
        ]);
    }
}