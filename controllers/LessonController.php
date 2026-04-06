<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class LessonController {
    
    private $lessonModel;
    
    public function __construct() {
        $this->lessonModel = new Lesson();
    }

    public function list() {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $lessons = $this->lessonModel->getAll();
        require_once __DIR__ . '/../views/admin/lessons_list.php';
    }

    public function showCreateForm() {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        require_once __DIR__ . '/../views/admin/create_lesson.php';
    }

    public function create() {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreateForm();
            return;
        }

        $title = $_POST['title'] ?? '';
        $coachName = $_POST['coach_name'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $maxStudents = $_POST['max_students'] ?? '';
        $level = $_POST['level'] ?? 'all';

        $errors = [];

        if (empty($title) || empty($coachName) || empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime) || empty($maxStudents)) {
            $errors[] = "Tous les champs sont obligatoires";
        }

        if (!is_numeric($maxStudents) || $maxStudents < 1) {
            $errors[] = "Le nombre maximum d'élèves doit être supérieur à 0";
        }

        if (!in_array($level, ['beginner', 'intermediate', 'advanced', 'all'])) {
            $errors[] = "Niveau invalide";
        }

        $startDatetime = $startDate . ' ' . $startTime;
        $endDatetime = $endDate . ' ' . $endTime;

        if (strtotime($endDatetime) <= strtotime($startDatetime)) {
            $errors[] = "La date de fin doit être après la date de début";
        }

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/admin/create_lesson.php';
            return;
        }

        $result = $this->lessonModel->create(
            $title,
            $coachName,
            $startDatetime,
            $endDatetime,
            $maxStudents,
            $level
        );

        if ($result) {
            header('Location: index.php?action=lessons-list');
            exit;
        } else {
            $errors[] = "Erreur lors de la création du cours";
            require_once __DIR__ . '/../views/admin/create_lesson.php';
        }
    }

    public function showEditForm($id) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $lesson = $this->lessonModel->getById($id);
        
        if (!$lesson) {
            header('Location: index.php?action=lessons-list');
            exit;
        }

        require_once __DIR__ . '/../views/admin/edit_lesson.php';
    }

    public function update($id) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showEditForm($id);
            return;
        }

        $title = $_POST['title'] ?? '';
        $coachName = $_POST['coach_name'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $maxStudents = $_POST['max_students'] ?? '';
        $level = $_POST['level'] ?? 'all';

        $errors = [];

        if (empty($title) || empty($coachName) || empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime) || empty($maxStudents)) {
            $errors[] = "Tous les champs sont obligatoires";
        }

        if (!is_numeric($maxStudents) || $maxStudents < 1) {
            $errors[] = "Le nombre maximum d'élèves doit être supérieur à 0";
        }

        $startDatetime = $startDate . ' ' . $startTime;
        $endDatetime = $endDate . ' ' . $endTime;

        if (!empty($errors)) {
            $lesson = $this->lessonModel->getById($id);
            require_once __DIR__ . '/../views/admin/edit_lesson.php';
            return;
        }

        $result = $this->lessonModel->update(
            $id,
            $title,
            $coachName,
            $startDatetime,
            $endDatetime,
            $maxStudents,
            $level
        );

        if ($result) {
            header('Location: index.php?action=lessons-list');
            exit;
        } else {
            $errors[] = "Erreur lors de la modification du cours";
            $lesson = $this->lessonModel->getById($id);
            require_once __DIR__ . '/../views/admin/edit_lesson.php';
        }
    }

    public function delete($id) {
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }

        $this->lessonModel->delete($id);
        header('Location: index.php?action=lessons-list');
        exit;
    }
}

?>