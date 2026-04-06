<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Student.php';

class StudentController {
    
    private $studentModel;
    
    public function __construct() {
        $this->studentModel = new Student();
    }

    public function showCompleteProfileForm() {
        require_once __DIR__ . '/../views/student/complete_profile.php';
    }

    public function completeProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCompleteProfileForm();
            return;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $country = $_POST['country'] ?? '';
        $level = $_POST['level'] ?? 'beginner';

        $errors = [];

        if (empty($firstName) || empty($lastName) || empty($country)) {
            $errors[] = "Tous les champs sont obligatoires";
        }

        if (!in_array($level, ['beginner', 'intermediate', 'advanced'])) {
            $errors[] = "Niveau invalide";
        }

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/student/complete_profile.php';
            return;
        }

        $result = $this->studentModel->create(
            $_SESSION['user']['id'],
            $firstName,
            $lastName,
            $country,
            $level
        );

        if ($result) {
            header('Location: index.php?action=client-dashboard');
            exit;
        } else {
            $errors[] = "Erreur lors de la création du profil";
            require_once __DIR__ . '/../views/student/complete_profile.php';
        }
    }

    public function list() {
        require_once __DIR__ . '/../controllers/AuthController.php';
        
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $students = $this->studentModel->getAll();
        require_once __DIR__ . '/../views/admin/students_list.php';
    }
}

?>