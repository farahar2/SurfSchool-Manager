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
    <title>Liste des élèves - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="navbar">
            <h1>🏄 Taghazout Surf - Admin</h1>
            <div class="nav-links">
                <a href="index.php?action=admin-dashboard">Dashboard</a>
                <a href="index.php?action=students-list" class="active">Élèves</a>
                <a href="index.php?action=logout" class="btn btn-logout">Déconnexion</a>
            </div>
        </nav>

        <div class="dashboard-content">
            <h2>Liste des élèves (<?= count($students) ?>)</h2>
            
            <?php if (empty($students)): ?>
                <p class="info-message">Aucun élève inscrit pour le moment.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Pays</th>
                            <th>Niveau</th>
                            <th>Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= $student['id'] ?></td>
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
                                        echo $levels[$student['level']] ?? $student['level'];
                                        ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($student['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>