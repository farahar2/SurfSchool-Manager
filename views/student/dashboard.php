<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!AuthController::isLoggedIn()) {
    header('Location: index.php?action=login');
    exit;
}

require_once __DIR__ . '/../../models/Student.php';
require_once __DIR__ . '/../../models/Enrollment.php';

$studentModel = new Student();
$enrollmentModel = new Enrollment();

$student = $studentModel->getByUserId($_SESSION['user']['id']);

// Si l'élève a un profil, compter ses cours
$totalLessons = 0;
$upcomingLessons = [];
if ($student) {
    $allLessons = $enrollmentModel->getStudentLessons($student['id']);
    $totalLessons = count($allLessons);
    
    // Filtrer les cours à venir
    foreach ($allLessons as $lesson) {
        if (strtotime($lesson['start_datetime']) >= time()) {
            $upcomingLessons[] = $lesson;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dashboard - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="navbar">
            <h1>🏄 Taghazout Surf</h1>
            <div class="nav-links">
                <a href="index.php?action=student-dashboard" class="active">Dashboard</a>
                <?php if ($student): ?>
                    <a href="index.php?action=my-lessons">Mes cours</a>
                <?php endif; ?>
                <span>Bienvenue, <?= htmlspecialchars($_SESSION['user']['email']) ?></span>
                <a href="index.php?action=logout" class="btn btn-logout">Déconnexion</a>
            </div>
        </nav>

        <div class="dashboard-content">
            <?php if ($student): ?>
                <!-- PROFIL COMPLÉTÉ -->
                <h2>Mon Profil</h2>
                
                <div class="profile-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p><strong>👤 Nom complet :</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></p>
                            <p><strong>🌍 Pays :</strong> <?= htmlspecialchars($student['country']) ?></p>
                            <p><strong>📊 Niveau :</strong> 
                                <span class="badge badge-<?= $student['level'] ?>">
                                    <?php
                                    $levels = [
                                        'beginner' => 'Débutant',
                                        'intermediate' => 'Intermédiaire',
                                        'advanced' => 'Avancé'
                                    ];
                                    echo $levels[$student['level']] ?? $student['level'];
                                    ?>
                                </span>
                            </p>
                            <p><strong>📧 Email :</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                            <p><strong>📅 Membre depuis :</strong> <?= date('d/m/Y', strtotime($student['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- STATISTIQUES -->
                <h2 style="margin-top: 40px;">Mes Statistiques</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?= $totalLessons ?></h3>
                        <p>Cours total</p>
                    </div>
                    
                    <div class="stat-card">
                        <h3><?= count($upcomingLessons) ?></h3>
                        <p>Cours à venir</p>
                    </div>
                    
                    <div class="stat-card">
                        <h3><?= $totalLessons - count($upcomingLessons) ?></h3>
                        <p>Cours terminés</p>
                    </div>
                </div>

                <!-- PROCHAINS COURS -->
                <h2 style="margin-top: 40px;">Mes prochains cours</h2>
                
                <?php if (empty($upcomingLessons)): ?>
                    <p class="info-message">
                        Vous n'avez pas de cours à venir. 
                        Contactez l'école pour vous inscrire à une session !
                    </p>
                <?php else: ?>
                    <div class="lessons-grid">
                        <?php 
                        // Afficher maximum 3 prochains cours
                        $displayedLessons = array_slice($upcomingLessons, 0, 3);
                        foreach ($displayedLessons as $lesson): 
                        ?>
                            <div class="lesson-card">
                                <h3><?= htmlspecialchars($lesson['title']) ?></h3>
                                <p><strong>🧑‍🏫 Coach :</strong> <?= htmlspecialchars($lesson['coach_name']) ?></p>
                                <p><strong>📅 Date :</strong> <?= date('d/m/Y', strtotime($lesson['start_datetime'])) ?></p>
                                <p><strong>⏰ Horaire :</strong> 
                                    <?= date('H:i', strtotime($lesson['start_datetime'])) ?> - 
                                    <?= date('H:i', strtotime($lesson['end_datetime'])) ?>
                                </p>
                                <p><strong>📊 Niveau :</strong> 
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
                                </p>
                                <p><strong>💳 Paiement :</strong> 
                                    <?php if ($lesson['payment_status'] == 'paid'): ?>
                                        <span class="badge badge-paid">✓ Payé</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">⏳ En attente</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($upcomingLessons) > 3): ?>
                        <div style="margin-top: 20px; text-align: center;">
                            <a href="index.php?action=my-lessons" class="btn btn-primary" style="width: auto;">
                                📚 Voir tous mes cours (<?= count($upcomingLessons) ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- ACTIONS RAPIDES -->
                <h2 style="margin-top: 40px;">Actions rapides</h2>
                <div class="actions">
                    <a href="index.php?action=my-lessons" class="btn btn-primary">
                        📚 Tous mes cours
                    </a>
                </div>

            <?php else: ?>
                <!-- PROFIL NON COMPLÉTÉ -->
                <h2>Bienvenue ! 👋</h2>
                
                <div class="alert alert-warning">
                    <h3 style="margin-top: 0;">Vous n'avez pas encore complété votre profil</h3>
                    <p>Pour pouvoir vous inscrire à des cours de surf, veuillez d'abord compléter votre profil avec vos informations personnelles.</p>
                    <div style="margin-top: 20px;">
                        <a href="index.php?action=complete-profile" class="btn btn-primary" style="width: auto;">
                            ✏️ Compléter mon profil
                        </a>
                    </div>
                </div>

                <div style="margin-top: 30px; background: white; padding: 30px; border-radius: 10px;">
                    <h3>Pourquoi compléter votre profil ?</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>🏄 Accéder aux cours de surf adaptés à votre niveau</li>
                        <li>📅 Consulter vos réservations et horaires</li>
                        <li>💳 Suivre vos paiements</li>
                        <li>📊 Voir vos statistiques et progression</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>