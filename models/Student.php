<?php

require_once __DIR__ . '/../config/Database.php';

class Student {
    private $db;
    private $id;
    private $user_id;
    private $first_name;
    private $last_name;
    private $country;
    private $level;
    private $created_at;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($userId, $firstName, $lastName, $country, $level) {
        $sql = "INSERT INTO students (user_id, first_name, last_name, country, level) 
                VALUES (:user_id, :first_name, :last_name, :country, :level)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':country' => $country,
            ':level' => $level
        ]);
    }

    public function getByUserId($userId) {
        $sql = "SELECT * FROM students WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function getById($id) {
        $sql = "SELECT * FROM students WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT s.*, u.email 
                FROM students s 
                JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $firstName, $lastName, $country, $level) {
        $sql = "UPDATE students 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    country = :country, 
                    level = :level 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':country' => $country,
            ':level' => $level
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM students WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getLevel() {
        return $this->level;
    }
}

?>