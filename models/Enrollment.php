<?php

require_once __DIR__ . '/../config/Database.php';

class Enrollment {
    private $db;
    private $id;
    private $student_id;
    private $lesson_id;
    private $payment_status;
    private $enrolled_at;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function enroll($studentId, $lessonId, $paymentStatus = 'pending') {
        $sql = "INSERT INTO enrollments (student_id, lesson_id, payment_status) 
                VALUES (:student_id, :lesson_id, :payment_status)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':student_id' => $studentId,
            ':lesson_id' => $lessonId,
            ':payment_status' => $paymentStatus
        ]);
    }

    public function getStudentLessons($studentId) {
        $sql = "SELECT e.*, l.title, l.coach_name, l.start_datetime, l.end_datetime, l.level
                FROM enrollments e
                JOIN lessons l ON e.lesson_id = l.id
                WHERE e.student_id = :student_id
                ORDER BY l.start_datetime DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function getLessonStudents($lessonId) {
        $sql = "SELECT e.*, s.first_name, s.last_name, s.level, s.country, u.email
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                JOIN users u ON s.user_id = u.id
                WHERE e.lesson_id = :lesson_id
                ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lesson_id' => $lessonId]);
        return $stmt->fetchAll();
    }

    public function isEnrolled($studentId, $lessonId) {
        $sql = "SELECT COUNT(*) as count FROM enrollments 
                WHERE student_id = :student_id AND lesson_id = :lesson_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':student_id' => $studentId,
            ':lesson_id' => $lessonId
        ]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function updatePaymentStatus($enrollmentId, $status) {
        $sql = "UPDATE enrollments SET payment_status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $enrollmentId,
            ':status' => $status
        ]);
    }

    public function delete($enrollmentId) {
        $sql = "DELETE FROM enrollments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $enrollmentId]);
    }

    public function countByLesson($lessonId) {
        $sql = "SELECT COUNT(*) as total FROM enrollments WHERE lesson_id = :lesson_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lesson_id' => $lessonId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function countPendingPayments() {
        $sql = "SELECT COUNT(*) as total FROM enrollments WHERE payment_status = 'pending'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getAll() {
        $sql = "SELECT e.*, 
                       s.first_name, s.last_name,
                       l.title as lesson_title, l.start_datetime
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                JOIN lessons l ON e.lesson_id = l.id
                ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

?>