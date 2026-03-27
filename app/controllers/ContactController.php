<?php

require_once __DIR__ . '/../../core/Controller.php';

class ContactController extends Controller {

    private $contactModel;

    public function __construct() {
        require_once __DIR__ . '/../models/ContactMessage.php';
        $this->contactModel = new ContactMessage();
    }

    // GET /contact
    public function index(): void {
        $this->view('contact/index', ['pageTitle' => 'Contact Us']);
    }

    // POST /contact/send
    public function send(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('contact');
        }

        $name    = trim(filter_input(INPUT_POST, 'name',    FILTER_SANITIZE_SPECIAL_CHARS));
        $email   = trim(filter_input(INPUT_POST, 'email',   FILTER_SANITIZE_EMAIL));
        $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

        $errors = [];

        if (strlen($name) < 2)                           $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'Please enter a valid email address.';
        if (strlen($message) < 10)                       $errors[] = 'Message must be at least 10 characters.';

        if (!empty($errors)) {
            $this->view('contact/index', [
                'pageTitle' => 'Contact Us',
                'errors'    => $errors,
                'name'      => $name,
                'email'     => $email,
                'message'   => $message,
            ]);
            return;
        }

        $success = $this->contactModel->create($name, $email, $message);

        if ($success) {
            // Pass timestamp for the "sent at" notice in your design
            $this->view('contact/index', [
                'pageTitle'  => 'Contact Us',
                'sentAt'     => date('d/m/Y H:i'),
            ]);
        } else {
            $this->view('contact/index', [
                'pageTitle' => 'Contact Us',
                'errors'    => ['Something went wrong. Please try again.'],
                'name'      => $name,
                'email'     => $email,
                'message'   => $message,
            ]);
        }
    }
}