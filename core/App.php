<?php
// core/App.php

class App {

    protected string $controller = 'HomeController';
    protected string $method     = 'index';
    protected array  $params     = [];

    public function __construct() {
        $url = $this->parseUrl();
        $this->dispatch($url);
    }

    private function parseUrl(): array {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    private function getRouteMap(): array {
        return [
            ''              => 'HomeController',
            'home'          => 'HomeController',
            'books'         => 'BookController',
            'user'          => 'UserController',
            'news'          => 'NewsController',
            'saved'         => 'SavedBooksController',
            'mybooks'       => 'SavedBooksController',
            'contact'       => 'ContactController',
            'admin'         => 'AdminController',
            'administrator' => 'AdminController',
            'borrow'        => 'BorrowController',  // POST /borrow/request
        ];
    }

    private function dispatch(array $url): void {
        $routeMap = $this->getRouteMap();

        $segment = strtolower($url[0] ?? '');
        if (array_key_exists($segment, $routeMap)) {
            $this->controller = $routeMap[$segment];
            array_shift($url);
        }

        $controllerFile = __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }
        require_once $controllerFile;

        $controllerInstance = new $this->controller();

        if (!empty($url[0])) {
            $methodName = $url[0];
            array_shift($url);

            if (method_exists($controllerInstance, $methodName)) {
                $reflection = new ReflectionMethod($controllerInstance, $methodName);
                if ($reflection->isPublic()) {
                    $this->method = $methodName;
                } else {
                    $this->notFound();
                    return;
                }
            } else {
                $this->notFound();
                return;
            }
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$controllerInstance, $this->method], $this->params);
    }

    private function notFound(): void {
        http_response_code(404);
        die('404 — Page not found.');
    }
}