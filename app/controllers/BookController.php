<?php

require_once __DIR__ . '/../../core/Controller.php';

class BookController extends Controller {

    public function index(): void {
        $this->view('books/index', ['title' => 'books']);
    }
}