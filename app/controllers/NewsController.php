<?php

require_once __DIR__ . '/../../core/Controller.php';

class NewsController extends Controller {

    private $newsModel;

    public function __construct() {
        // Load the model manually — your MVC uses $this->model() only if
        // you've wired that helper; this is the safe explicit fallback.
        require_once __DIR__ . '/../models/News.php';
        $this->newsModel = new News();
    }

    // GET /news
    public function index(): void {
        // Fetch stats for the header section
        $totalBooks    = $this->newsModel->getTotalBooks();
        $totalBorrowed = $this->newsModel->getTotalBorrowed();
        $totalUsers    = $this->newsModel->getTotalUsers();

        // First 3 go to the featured carousel, the rest to the grid
        $allNews       = $this->newsModel->getRecent(9);
        $featuredNews  = array_slice($allNews, 0, 3);
        $gridNews      = array_slice($allNews, 3);

        $this->view('news/index', [
            'pageTitle'     => 'News',
            'totalBooks'    => $totalBooks,
            'totalBorrowed' => $totalBorrowed,
            'totalUsers'    => $totalUsers,
            'featuredNews'  => $featuredNews,
            'gridNews'      => $gridNews,
        ]);
    }
}