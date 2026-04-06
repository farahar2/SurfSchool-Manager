<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
    header('Location: index.php?action=login');
    exit;
}

require_once __DIR__ . '/../../models/Student.php';
require_once __DIR__ . '/../../models/Lesson.php';
require_once __DIR__ . '/../../models/Enrollment.php';

$studentModel = new Student();
$lessonModel = new Lesson();
$enrollmentModel = new Enrollment();

// Récupérer les statistiques
$students = $studentModel->getAll();
$totalStudents = count($students);

$lessons = $lessonModel->getAll();
$totalLessons = count($lessons);

// Compter les cours à venir
$upcomingLessons = $lessonModel->getUpcoming();
$totalUpcomingLessons = count($upcomingLessons);

// Compter les paiements en attente
$pendingPayments = $enrollmentModel->countPendingPayments();

// Récupérer toutes les inscriptions
$allEnrollments = $enrollmentModel->getAll();
$totalEnrollments = count($allEnrollments);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="navbar">
            <h1>🏄 Taghazout Surf - Admin</h1>
            <div class="nav-links">
                <a href="index.php?action=admin-dashboard" class="active">Dashboard</a>
                <a href="index.php?action=students-list">Élèves</a>
                <a href="index.php?action=lessons-list">Cours</a>
                <span>Admin: <?= htmlspecialchars($_SESSION['user']['email']) ?></span>
                <a href="index.php?action=logout" class="btn btn-logout">Déconnexion</a>
            </div>
        </nav>

        <div class="dashboard-content">
            <h2>Tableau de bord</h2>
            
            <!-- STATISTIQUES PRINCIPALES -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <h3><?= $totalStudents ?></h3>
                    <p>Élèves inscrits</p>
                    <a href="index.php?action=students-list" class="stat-link">Voir la liste →</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🏄</div>
                    <h3><?= $totalLessons ?></h3>
                    <p>Cours créés</p>
                    <a href="index.php?action=lessons-list" class="stat-link">Voir la liste →</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <h3><?= $totalUpcomingLessons ?></h3>
                    <p>Cours à venir</p>
                    <a href="index.php?action=lessons-list" class="stat-link">Gérer les cours →</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <h3><?= $totalEnrollments ?></h3>
                    <p>Inscriptions totales</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">💳</div>
                    <h3><?= $pendingPayments ?></h3>
                    <p>Paiements en attente</p>
                    <?php if ($pendingPayments > 0): ?>
                        <span class="stat-warning">⚠️ À traiter</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ACTIONS RAPIDES -->
            <h2 style="margin-top: 40px;">Actions rapides</h2>
            <div class="actions">
                <a href="index.php?action=create-lesson" class="btn btn-primary">
                    ➕ Créer un cours
                </a>
                <a href="index.php?action=students-list" class="btn btn-primary">
                    👥 Voir tous les élèves
                </a>
                <a href="index.php?action=lessons-list" class="btn btn-primary">
                    🏄 Voir tous les cours
                </a>
            </div>

            <!-- PROCHAINS COURS -->
            <h2 style="margin-top: 40px;">Prochains cours (<?= count($upcomingLessons) ?>)</h2>
            
            <?php if (empty($upcomingLessons)): ?>
                <p class="info-message">Aucun cours programmé pour le moment.</p>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Coach</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Niveau</th>
                                <th>Inscrits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Afficher les 5 prochains cours
                            $displayedLessons = array_slice($upcomingLessons, 0, 5);
                            foreach ($displayedLessons as $lesson): 
                                $enrolledCount = $enrollmentModel->countByLesson($lesson['id']);
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($lesson['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($lesson['coach_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($lesson['start_datetime'])) ?></td>
                                    <td>
                                        <?= date('H:i', strtotime($lesson['start_datetime'])) ?> - 
                                        <?= date('H:i', strtotime($lesson['end_datetime'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $lesson['level'] ?>">
                                            <?php
                                            $levels = [
                                                'beginner' => 'Débutant',
                                                'intermediate' => 'Intermédiaire',
                                                'advanced' => 'Avancé',
                                                'all' => 'Tous niveaux'
                                            ];
                                            echo $levels[$lesson['level']];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="enrollment-count">
                                            <?= $enrolledCount ?> / <?= $lesson['max_students'] ?>
                                        </span>
                                        <?php if ($enrolledCount >= $lesson['max_students']): ?>
                                            <span class="badge badge-full">Complet</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?action=lesson-details&id=<?= $lesson['id'] ?>" 
                                           class="btn-action btn-view">
                                            Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($upcomingLessons) > 5): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="index.php?action=lessons-list" class="btn btn-secondary" style="width: auto;">
                            Voir tous les cours (<?= count($upcomingLessons) ?>)
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- DERNIERS ÉLÈVES INSCRITS -->
            <h2 style="margin-top: 40px;">Derniers élèves inscrits</h2>
            
            <?php if (empty($students)): ?>
                <p class="info-message">Aucun élève inscrit pour le moment.</p>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nom complet</th>
                                <th>Email</th>
                                <th>Pays</th>
                                <th>Niveau</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Afficher les 5 derniers élèves
                            $recentStudents = array_slice($students, 0, 5);
                            foreach ($recentStudents as $student): 
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['country']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $student['level'] ?>">
                                            <?php
                                            $levels = [
                                                'beginner' => 'Débutant',
                                                'intermediate' => 'Intermédiaire',
                                                'advanced' => 'Avancé'
                                            ];
                                            echo $levels[$student['level']];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($student['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($students) > 5): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="index.php?action=students-list" class="btn btn-secondary" style="width: auto;">
                            Voir tous les élèves (<?= count($students) ?>)
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- RÉSUMÉ DES INSCRIPTIONS -->
            <?php if (!empty($allEnrollments)): ?>
                <h2 style="margin-top: 40px;">Dernières inscriptions</h2>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Élève</th>
                                <th>Cours</th>
                                <th>Date du cours</th>
                                <th>Statut paiement</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Afficher les 5 dernières inscriptions
                            $recentEnrollments = array_slice($allEnrollments, 0, 5);
                            foreach ($recentEnrollments as $enrollment): 
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></td>
                                    <td><?= htmlspecialchars($enrollment['lesson_title']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($enrollment['start_datetime'])) ?></td>
                                    <td>
                                        <?php if ($enrollment['payment_status'] == 'paid'): ?>
                                            <span class="badge badge-paid">✓ Payé</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">⏳ En attente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($enrollment['enrolled_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>