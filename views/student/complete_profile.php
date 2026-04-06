<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complétez votre profil - Taghazout Surf</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>🏄 Taghazout Surf Expo</h1>
            <h2>Complétez votre profil</h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?action=complete-profile">
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="country">Pays</label>
                    <input type="text" id="country" name="country" 
                           value="<?= htmlspecialchars($_POST['country'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="level">Niveau de surf</label>
                    <select id="level" name="level" required>
                        <option value="beginner">Débutant</option>
                        <option value="intermediate">Intermédiaire</option>
                        <option value="advanced">Avancé</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Valider</button>
            </form>
        </div>
    </div>
</body>
</html>