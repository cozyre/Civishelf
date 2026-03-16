<?php

class UserController extends Controller {

    private $userModel;

    public function __construct() {
        // Require session to be started before this controller is used.
        // Start it in your App/index.php if not already done.
        $this->userModel = $this->model('User');
    }

    // -----------------------------------------------------------------------
    // GET /user/login
    // -----------------------------------------------------------------------
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        // Already logged in → redirect away
        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }

        $this->view('auth/login', ['pageTitle' => 'Login']);
    }

    // -----------------------------------------------------------------------
    // POST /user/login  (called internally by login())
    // -----------------------------------------------------------------------
    private function handleLogin() {
        $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

        $errors = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (!empty($errors)) {
            $this->view('auth/login', ['errors' => $errors, 'email' => $email, 'pageTitle' => 'Login']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user->password)) {
            $this->view('auth/login', [
                'errors'    => ['Invalid email or password.'],
                'email'     => $email,
                'pageTitle' => 'Login'
            ]);
            return;
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        flash('success', 'Welcome back, ' . htmlspecialchars($user->name) . '!');
        redirect('/');
    }

    // -----------------------------------------------------------------------
    // GET /user/register
    // -----------------------------------------------------------------------
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
            return;
        }

        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }

        $this->view('auth/register', ['pageTitle' => 'Register']);
    }

    // -----------------------------------------------------------------------
    // POST /user/register  (called internally by register())
    // -----------------------------------------------------------------------
    private function handleRegister() {
        $name     = trim(filter_input(INPUT_POST, 'name',             FILTER_SANITIZE_SPECIAL_CHARS));
        $email    = trim(filter_input(INPUT_POST, 'email',            FILTER_SANITIZE_EMAIL));
        $password = trim(filter_input(INPUT_POST, 'password',         FILTER_DEFAULT));
        $confirm  = trim(filter_input(INPUT_POST, 'confirm_password', FILTER_DEFAULT));

        $errors = [];

        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors) && $this->userModel->emailExists($email)) {
            $errors[] = 'That email is already registered. Please log in.';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'errors'    => $errors,
                'name'      => $name,
                'email'     => $email,
                'pageTitle' => 'Register'
            ]);
            return;
        }

        $newId = $this->userModel->create($name, $email, $password);

        if (!$newId) {
            $this->view('auth/register', [
                'errors'    => ['Registration failed. Please try again.'],
                'pageTitle' => 'Register'
            ]);
            return;
        }

        flash('success', 'Account created! You can now log in.');
        redirect('/user/login');
    }

    // -----------------------------------------------------------------------
    // GET /user/logout
    // -----------------------------------------------------------------------
    public function logout() {
        // Destroy only user session data, not admin
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_destroy();
        flash('success', 'You have been logged out.');
        redirect('/user/login');
    }
}