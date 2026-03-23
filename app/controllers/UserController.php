<?php

class UserController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // -----------------------------------------------------------------------
    // POST /user/login  (form is inside the modal in header.php)
    // On success → redirect to intended page or home.
    // On failure → set session flags so header.php reopens the modal, then
    //              redirect back to the page the user was on (HTTP_REFERER).
    // -----------------------------------------------------------------------
    public function login() {
        // Only accept POST — GET requests to this URL go home
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/');
        }

        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

        // Basic validation
        if (empty($email) || empty($password)) {
            $this->failLogin('All fields are required.', $email);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user->password_hash)) {
            $this->failLogin('Invalid email or password.', $email);
            return;
        }

        if ($user->user_status === 'banned') {
            $this->failLogin('Your account has been suspended. Contact support.', $email);
            return;
        }

        // Success — regenerate session to prevent fixation
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user->user_id;
        $_SESSION['user_name'] = $user->user_name;
        $_SESSION['user_role'] = $user->role;

        flash('success', 'Welcome back, ' . htmlspecialchars($user->user_name) . '!');
        redirect('/');
    }

    // -----------------------------------------------------------------------
    // GET  /user/register  → show form
    // POST /user/register  → process
    // -----------------------------------------------------------------------
    public function register() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
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

        if (strlen($name) < 2)                              $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'Please enter a valid email address.';
        if (strlen($password) < 8)                          $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)                         $errors[] = 'Passwords do not match.';

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
        redirect('/');  // Redirect home; the modal can be opened from there
    }

    // -----------------------------------------------------------------------
    // GET /user/logout
    // -----------------------------------------------------------------------
    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_regenerate_id(true);
        flash('success', 'You have been logged out.');
        redirect('/');
    }

    // ---------------- -------------------------------------------------------
    // Private helper — failed login redirect
    // Sets session flags that header.php reads to reopen the modal with an
    // inline error message and prefilled email.
    // -----------------------------------------------------------------------
    private function failLogin(string $message, string $email = ''): void {
        $_SESSION['login_failed']  = true;
        $_SESSION['login_error']   = $message;
        $_SESSION['login_prefill'] = $email;

        // Go back to wherever they were (the referer), fallback to home
        $back = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($back);
    }
}