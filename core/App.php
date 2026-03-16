<?php
// core/App.php
// Parses the URL and dispatches to the correct controller + method

class App {

    protected string $controller = 'HomeController';
    protected string $method     = 'index';
    protected array  $params     = [];

    public function __construct() {
        $url = $this->parseUrl();
        $this->dispatch($url);
    }

    // -------------------------------------------------------
    // Parse URL into segments
    // e.g. /books/show/5 → ['books', 'show', '5']
    // -------------------------------------------------------
    private function parseUrl(): array {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    // -------------------------------------------------------
    // Route map
    // Maps URL segment[0] → Controller class name
    // Add new controllers here as you build them
    // -------------------------------------------------------
    private function getRouteMap(): array {
        return [
            ''        => 'HomeController',
            'home'    => 'HomeController',
            'books'   => 'BookController',
            'user'    => 'UserController',
            'news'    => 'NewsController',
            'saved'   => 'SaveController',
            'contact' => 'ContactController',
            'admin'   => 'AdminController',
        ];
    }

    // -------------------------------------------------------
    // Dispatcher
    // -------------------------------------------------------
    private function dispatch(array $url): void {
        $routeMap = $this->getRouteMap();

        // Segment 0 → controller
        $segment = strtolower($url[0] ?? '');
        if (array_key_exists($segment, $routeMap)) {
            $this->controller = $routeMap[$segment];
            array_shift($url);
        }

        // Load controller file
        $controllerFile = __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }
        require_once $controllerFile;

        $controllerInstance = new $this->controller();

        // Segment 1 → method
        if (!empty($url[0])) {
            $methodName = $url[0];
            array_shift($url);

            // Only call public methods — prevents calling internal helpers via URL
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

        // Remaining segments → parameters
        $this->params = $url ? array_values($url) : [];

        // Call controller method with params
        call_user_func_array([$controllerInstance, $this->method], $this->params);
    }

    // -------------------------------------------------------
    // 404 handler
    // -------------------------------------------------------
    private function notFound(): void {
        http_response_code(404);
        // You can replace this with a proper 404 view later
        die('404 — Page not found.');
    }
}