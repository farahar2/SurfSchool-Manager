<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

if (!AuthController::isLoggedIn() || !AuthController::isAdmin()) {
    header('Location: index.php?action=login');
    exit;
}

// Séparer date et heure pour les champs du formulaire
$startParts = explode(' ', $lesson['start_datetime']);
$endParts = explode(' ', $lesson['end_datetime']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un cours - Taghazout Surf</title>
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
            <h2>Modifier le cours</h2>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" action="index.php?action=edit-lesson&id=<?= $lesson['id'] ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre du cours *</label>
                            <input type="text" id="title" name="title" 
                                   value="<?= htmlspecialchars($_POST['title'] ?? $lesson['title']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="coach_name">Nom du coach *</label>
                            <input type="text" id="coach_name" name="coach_name" 
                                   value="<?= htmlspecialchars($_POST['coach_name'] ?? $lesson['coach_name']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Date de début *</label>
                            <input type="date" id="start_date" name="start_date" 
                                   value="<?= htmlspecialchars($_POST['start_date'] ?? $startParts[0]) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="start_time">Heure de début *</label>
                            <input type="time" id="start_time" name="start_time" 
                                   value="<?= htmlspecialchars($_POST['start_time'] ?? $startParts[1]) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="end_date">Date de fin *</label>
                            <input type="date" id="end_date" name="end_date" 
                                   value="<?= htmlspecialchars($_POST['end_date'] ?? $endParts[0]) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end_time">Heure de fin *</label>
                            <input type="time" id="end_time" name="end_time" 
                                   value="<?= htmlspecialchars($_POST['end_time'] ?? $endParts[1]) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_students">Nombre maximum d'élèves *</label>
                            <input type="number" id="max_students" name="max_students" 
                                   value="<?= htmlspecialchars($_POST['max_students'] ?? $lesson['max_students']) ?>" 
                                   min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="level">Niveau requis *</label>
                            <select id="level" name="level" required>
                                <?php $currentLevel = $_POST['level'] ?? $lesson['level']; ?>
                                <option value="all" <?= $currentLevel == 'all' ? 'selected' : '' ?>>Tous niveaux</option>
                                <option value="beginner" <?= $currentLevel == 'beginner' ? 'selected' : '' ?>>Débutant</option>
                                <option value="intermediate" <?= $currentLevel == 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                                <option value="advanced" <?= $currentLevel == 'advanced' ? 'selected' : '' ?>>Avancé</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="index.php?action=lessons-list" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>