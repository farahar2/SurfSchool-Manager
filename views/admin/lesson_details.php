<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du cours - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="navbar">
            <h1>🏄 Taghazout Surf - Admin</h1>
            <div class="nav-links">
                <a href="index.php?action=admin-dashboard">Dashboard</a>
                <a href="index.php?action=students-list">Élèves</a>
                <a href="index.php?action=lessons-list">Cours</a>
                <a href="index.php?action=logout" class="btn btn-logout">Déconnexion</a>
            </div>
        </nav>

        <div class="dashboard-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Détails du cours</h2>
                <a href="index.php?action=lessons-list" class="btn btn-secondary" style="width: auto;">← Retour à la liste</a>
            </div>

            <div class="lesson-info-card">
                <h3><?= htmlspecialchars($lesson['title']) ?></h3>
                <div class="lesson-details-grid">
                    <div>
                        <p><strong>Coach :</strong> <?= htmlspecialchars($lesson['coach_name']) ?></p>
                        <p><strong>Début :</strong> <?= date('d/m/Y à H:i', strtotime($lesson['start_datetime'])) ?></p>
                        <p><strong>Fin :</strong> <?= date('d/m/Y à H:i', strtotime($lesson['end_datetime'])) ?></p>
                    </div>
                    <div>
                        <p><strong>Niveau :</strong> 
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
                        <p><strong>Inscrits :</strong> <?= $totalEnrolled ?> / <?= $lesson['max_students'] ?></p>
                        <p><strong>Places restantes :</strong> <?= $availableSpots ?></p>
                    </div>
                </div>

                <?php if ($availableSpots > 0): ?>
                    <div style="margin-top: 20px;">
                        <a href="index.php?action=enroll-student&lesson_id=<?= $lesson['id'] ?>" class="btn btn-primary" style="width: auto;">
                            + Inscrire un élève
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" style="margin-top: 20px;">
                        Ce cours est complet.
                    </div>
                <?php endif; ?>
            </div>

            <h3 style="margin-top: 40px;">Élèves inscrits (<?= $totalEnrolled ?>)</h3>

            <?php if (empty($enrolledStudents)): ?>
                <p class="info-message">Aucun élève inscrit pour le moment.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Pays</th>
                            <th>Niveau</th>
                            <th>Statut paiement</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolledStudents as $enrollment): ?>
                            <tr>
                                <td><?= htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']) ?></td>
                                <td><?= htmlspecialchars($enrollment['email']) ?></td>
                                <td><?= htmlspecialchars($enrollment['country']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $enrollment['level'] ?>">
                                        <?php
                                        $levels = [
                                            'beginner' => 'Débutant',
                                            'intermediate' => 'Intermédiaire',
                                            'advanced' => 'Avancé'
                                        ];
                                        echo $levels[$enrollment['level']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($enrollment['payment_status'] == 'paid'): ?>
                                        <span class="badge badge-paid">Payé</span>
                                        <a href="index.php?action=update-payment&id=<?= $enrollment['id'] ?>&status=pending&lesson_id=<?= $lesson['id'] ?>" 
                                           class="btn-action btn-edit" style="font-size: 12px; margin-left: 5px;">
                                            Marquer en attente
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-pending">En attente</span>
                                        <a href="index.php?action=update-payment&id=<?= $enrollment['id'] ?>&status=paid&lesson_id=<?= $lesson['id'] ?>" 
                                           class="btn-action btn-edit" style="font-size: 12px; margin-left: 5px;">
                                            Marquer payé
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($enrollment['enrolled_at'])) ?></td>
                                <td>
                                    <a href="index.php?action=delete-enrollment&id=<?= $enrollment['id'] ?>&lesson_id=<?= $lesson['id'] ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Êtes-vous sûr de vouloir désinscrire cet élève ?')">
                                        Désinscrire
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>