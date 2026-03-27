<?php

class ContactMessage {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(string $name, string $email, string $message): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO contact_messages (sender_name, email, message, created_at)
             VALUES (:name, :email, :message, NOW())'
        );
        $stmt->bindValue(':name',    $name);
        $stmt->bindValue(':email',   $email);
        $stmt->bindValue(':message', $message);
        return $stmt->execute();
    }
}