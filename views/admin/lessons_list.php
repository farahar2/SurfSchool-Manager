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
    <title>Liste des cours - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="navbar">
            <h1>🏄 Taghazout Surf - Admin</h1>
            <div class="nav-links">
                <a href="index.php?action=admin-dashboard">Dashboard</a>
                <a href="index.php?action=students-list">Élèves</a>
                <a href="index.php?action=lessons-list" class="active">Cours</a>
                <a href="index.php?action=logout" class="btn btn-logout">Déconnexion</a>
            </div>
        </nav>

        <div class="dashboard-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Liste des cours (<?= count($lessons) ?>)</h2>
                <a href="index.php?action=create-lesson" class="btn btn-primary" style="width: auto;">+ Créer un cours</a>
            </div>
            
            <?php if (empty($lessons)): ?>
                <p class="info-message">Aucun cours créé pour le moment.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Coach</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Niveau</th>
                            <th>Places max</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td><?= $lesson['id'] ?></td>
                                <td><?= htmlspecialchars($lesson['title']) ?></td>
                                <td><?= htmlspecialchars($lesson['coach_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($lesson['start_datetime'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($lesson['end_datetime'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $lesson['level'] ?>">
                                        <?php
                                        $levels = [
                                            'beginner' => 'Débutant',
                                            'intermediate' => 'Intermédiaire',
                                            'advanced' => 'Avancé',
                                            'all' => 'Tous niveaux'
                                        ];
                                        echo $levels[$lesson['level']] ?? $lesson['level'];
                                        ?>
                                    </span>
                                </td>
                                <td><?= $lesson['max_students'] ?></td>
                                <td>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="index.php?action=lesson-details&id=<?= $lesson['id'] ?>" class="btn-action btn-view">Détails</a>
        <a href="index.php?action=edit-lesson&id=<?= $lesson['id'] ?>" class="btn-action btn-edit">Modifier</a>
        <a href="index.php?action=delete-lesson&id=<?= $lesson['id'] ?>" 
           class="btn-action btn-delete"
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')">
            Supprimer
        </a>
    </div>
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