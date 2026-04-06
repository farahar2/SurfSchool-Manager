<?php
session_start();

$action = $_GET['action'] ?? 'login';

switch ($action) {
    
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLoginForm();
        }
        break;
    
    case 'register':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegisterForm();
        }
        break;
    
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case 'complete-profile':
        require_once 'controllers/StudentController.php';
        require_once 'controllers/AuthController.php';
        
        if (!AuthController::isLoggedIn()) {
            header('Location: index.php?action=login');
            exit;
        }
        
        $controller = new StudentController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->completeProfile();
        } else {
            $controller->showCompleteProfileForm();
        }
        break;
    
    case 'admin-dashboard':
        require_once 'controllers/AuthController.php';
        
        if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
            header('Location: index.php?action=login');
            exit;
        }
        
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    
    case 'student-dashboard':
        require_once 'controllers/AuthController.php';
        
        if (!AuthController::isLoggedIn()) {
            header('Location: index.php?action=login');
            exit;
        }
        
        require_once __DIR__ . '/views/student/dashboard.php';
        break;
    
    case 'students-list':
        require_once 'controllers/StudentController.php';
        $controller = new StudentController();
        $controller->list();
        break;

        case 'lessons-list':
    require_once 'controllers/LessonController.php';
    $controller = new LessonController();
    $controller->list();
    break;

case 'create-lesson':
    require_once 'controllers/LessonController.php';
    $controller = new LessonController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->create();
    } else {
        $controller->showCreateForm();
    }
    break;

case 'edit-lesson':
    require_once 'controllers/LessonController.php';
    $id = $_GET['id'] ?? 0;
    $controller = new LessonController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->update($id);
    } else {
        $controller->showEditForm($id);
    }
    break;

case 'delete-lesson':
    require_once 'controllers/LessonController.php';
    $id = $_GET['id'] ?? 0;
    $controller = new LessonController();
    $controller->delete($id);
    break;

    case 'enroll-student':
    require_once 'controllers/EnrollmentController.php';
    $lessonId = $_GET['lesson_id'] ?? 0;
    $controller = new EnrollmentController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->enroll($lessonId);
    } else {
        $controller->showEnrollForm($lessonId);
    }
    break;

case 'lesson-details':
    require_once 'controllers/EnrollmentController.php';
    $lessonId = $_GET['id'] ?? 0;
    $controller = new EnrollmentController();
    $controller->showLessonDetails($lessonId);
    break;

case 'update-payment':
    require_once 'controllers/EnrollmentController.php';
    $enrollmentId = $_GET['id'] ?? 0;
    $controller = new EnrollmentController();
    $controller->updatePaymentStatus($enrollmentId);
    break;

case 'delete-enrollment':
    require_once 'controllers/EnrollmentController.php';
    $enrollmentId = $_GET['id'] ?? 0;
    $controller = new EnrollmentController();
    $controller->deleteEnrollment($enrollmentId);
    break;

case 'my-lessons':
    require_once 'controllers/EnrollmentController.php';
    $controller = new EnrollmentController();
    $controller->myLessons();
    break;
    
    default:
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->showLoginForm();
        break;
}

?>