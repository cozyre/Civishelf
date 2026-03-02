<?php
// core/Database.php

require_once __DIR__ . '/../config/config.php';

class Database {
    private static ?PDO $instance = null;

    // Singleton — one connection for the whole request
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Don't expose DB errors to the browser
                error_log('DB Connection failed: ' . $e->getMessage());
                http_response_code(500);
                die(json_encode(['success' => false, 'message' => 'Database connection error.']));
            }
        }

        return self::$instance;
    }

    // Prevent instantiation and cloning
    private function __construct() {}
    private function __clone() {}
}

// $db = Database::getInstance();
// $stmt = $db->prepare('SELECT * FROM books WHERE book_id = ?');
// $stmt->execute([$id]);
// $book = $stmt->fetch();