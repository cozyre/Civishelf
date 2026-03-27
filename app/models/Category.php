<?php

class Category {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // -----------------------------------------------------------------------
    // All categories — used to populate filter chips on Home and Explore pages.
    // -----------------------------------------------------------------------
    public function getAll(): array {
        $stmt = $this->db->prepare(
            'SELECT category_id, category_name, description
             FROM categories
             ORDER BY category_name ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}