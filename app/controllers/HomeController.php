<?php

require_once __DIR__ . '/../../core/Controller.php';

class HomeController extends Controller {

    public function index(): void {
        $bookModel     = $this->model('Book');
        $categoryModel = $this->model('Category');

        // Top-searched carousel — ordered by save count
        $topBooks   = $bookModel->getPopularBySaves(10);

        // Category filter chips
        $categories = $categoryModel->getAll();

        // Active category from query string — null means "show all"
        $activeCategoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;

        // Major Needs grid — filtered by category if one is selected
        $filteredBooks = $bookModel->getAll(8, 0, $activeCategoryId);

        $this->view('home/index', [
            'pageTitle'        => 'Home',
            'topBooks'         => $topBooks,
            'categories'       => $categories,
            'filteredBooks'    => $filteredBooks,
            'activeCategoryId' => $activeCategoryId,
        ]);
    }
}