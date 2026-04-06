<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class EnrollmentController {
    
    private $enrollmentModel;
    private $studentModel;
    private $lessonModel;
    
    public function __construct() {
        $this->enrollmentModel = new Enrollment();
        $this->studentModel = new Student();
        $this->lessonModel = new Lesson();
    }

    public function showEnrollForm($lessonId) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $lesson = $this->lessonModel->getById($lessonId);
        if (!$lesson) {
            header('Location: index.php?action=lessons-list');
            exit;
        }

        $students = $this->studentModel->getAll();
        $enrolledStudents = $this->enrollmentModel->getLessonStudents($lessonId);
        
        $enrolledIds = array_column($enrolledStudents, 'student_id');
        $availableStudents = array_filter($students, function($student) use ($enrolledIds) {
            return !in_array($student['id'], $enrolledIds);
        });

        require_once __DIR__ . '/../views/admin/enroll_student.php';
    }

    public function enroll($lessonId) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showEnrollForm($lessonId);
            return;
        }

        $studentId = $_POST['student_id'] ?? 0;
        $paymentStatus = $_POST['payment_status'] ?? 'pending';

        $errors = [];

        if (empty($studentId)) {
            $errors[] = "Veuillez sélectionner un élève";
        }

        $lesson = $this->lessonModel->getById($lessonId);
        if (!$lesson) {
            $errors[] = "Cours introuvable";
        }

        if ($this->enrollmentModel->isEnrolled($studentId, $lessonId)) {
            $errors[] = "Cet élève est déjà inscrit à ce cours";
        }

        $enrolledCount = $this->enrollmentModel->countByLesson($lessonId);
        if ($enrolledCount >= $lesson['max_students']) {
            $errors[] = "Le cours est complet";
        }

        if (!empty($errors)) {
            $students = $this->studentModel->getAll();
            $enrolledStudents = $this->enrollmentModel->getLessonStudents($lessonId);
            $enrolledIds = array_column($enrolledStudents, 'student_id');
            $availableStudents = array_filter($students, function($student) use ($enrolledIds) {
                return !in_array($student['id'], $enrolledIds);
            });
            require_once __DIR__ . '/../views/admin/enroll_student.php';
            return;
        }

        $result = $this->enrollmentModel->enroll($studentId, $lessonId, $paymentStatus);

        if ($result) {
            header('Location: index.php?action=lesson-details&id=' . $lessonId);
            exit;
        } else {
            $errors[] = "Erreur lors de l'inscription";
            $students = $this->studentModel->getAll();
            $enrolledStudents = $this->enrollmentModel->getLessonStudents($lessonId);
            $enrolledIds = array_column($enrolledStudents, 'student_id');
            $availableStudents = array_filter($students, function($student) use ($enrolledIds) {
                return !in_array($student['id'], $enrolledIds);
            });
            require_once __DIR__ . '/../views/admin/enroll_student.php';
        }
    }

    public function showLessonDetails($lessonId) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $lesson = $this->lessonModel->getById($lessonId);
        if (!$lesson) {
            header('Location: index.php?action=lessons-list');
            exit;
        }

        $enrolledStudents = $this->enrollmentModel->getLessonStudents($lessonId);
        $totalEnrolled = count($enrolledStudents);
        $availableSpots = $lesson['max_students'] - $totalEnrolled;

        require_once __DIR__ . '/../views/admin/lesson_details.php';
    }

    public function updatePaymentStatus($enrollmentId) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $status = $_GET['status'] ?? 'pending';
        $lessonId = $_GET['lesson_id'] ?? 0;

        $this->enrollmentModel->updatePaymentStatus($enrollmentId, $status);
        
        header('Location: index.php?action=lesson-details&id=' . $lessonId);
        exit;
    }

    public function deleteEnrollment($enrollmentId) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $lessonId = $_GET['lesson_id'] ?? 0;
        
        $this->enrollmentModel->delete($enrollmentId);
        
        header('Location: index.php?action=lesson-details&id=' . $lessonId);
        exit;
    }

    public function myLessons() {
        if (!AuthController::isLoggedIn()) {
            header('Location: index.php?action=login');
            exit;
        }

        $student = $this->studentModel->getByUserId($_SESSION['user']['id']);
        
        if (!$student) {
            header('Location: index.php?action=complete-profile');
            exit;
        }

        $lessons = $this->enrollmentModel->getStudentLessons($student['id']);

        require_once __DIR__ . '/../views/student/my_lessons.php';
    }
}

?>