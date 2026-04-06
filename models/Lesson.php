<?php

require_once __DIR__ . '/../config/Database.php';

class Lesson {
    private $db;
    private $id;
    private $title;
    private $coach_name;
    private $start_datetime;
    private $end_datetime;
    private $max_students;
    private $level;
    private $created_at;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($title, $coachName, $startDatetime, $endDatetime, $maxStudents, $level) {
        $sql = "INSERT INTO lessons (title, coach_name, start_datetime, end_datetime, max_students, level) 
                VALUES (:title, :coach_name, :start_datetime, :end_datetime, :max_students, :level)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':title' => $title,
            ':coach_name' => $coachName,
            ':start_datetime' => $startDatetime,
            ':end_datetime' => $endDatetime,
            ':max_students' => $maxStudents,
            ':level' => $level
        ]);
    }

    public function getAll() {
        $sql = "SELECT * FROM lessons ORDER BY start_datetime DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM lessons WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUpcoming() {
        $sql = "SELECT * FROM lessons 
                WHERE start_datetime >= NOW() 
                ORDER BY start_datetime ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $title, $coachName, $startDatetime, $endDatetime, $maxStudents, $level) {
        $sql = "UPDATE lessons 
                SET title = :title, 
                    coach_name = :coach_name, 
                    start_datetime = :start_datetime, 
                    end_datetime = :end_datetime, 
                    max_students = :max_students, 
                    level = :level 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':coach_name' => $coachName,
            ':start_datetime' => $startDatetime,
            ':end_datetime' => $endDatetime,
            ':max_students' => $maxStudents,
            ':level' => $level
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM lessons WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function countEnrolled($lessonId) {
        $sql = "SELECT COUNT(*) as total FROM enrollments WHERE lesson_id = :lesson_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lesson_id' => $lessonId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getAvailableSpots($lessonId) {
        $lesson = $this->getById($lessonId);
        if (!$lesson) {
            return 0;
        }
        $enrolled = $this->countEnrolled($lessonId);
        return $lesson['max_students'] - $enrolled;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCoachName() {
        return $this->coach_name;
    }

    public function getStartDatetime() {
        return $this->start_datetime;
    }

    public function getEndDatetime() {
        return $this->end_datetime;
    }

    public function getMaxStudents() {
        return $this->max_students;
    }

    public function getLevel() {
        return $this->level;
    }
}

?>