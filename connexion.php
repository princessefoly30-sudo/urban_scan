<?php
require_once __DIR__ . '/includes/auth.php';

startSession();
if (isLoggedIn()) {
    header('Location: liste.php');
    exit;
}

$authFile = __DIR__ . '/config/auth.php';
if (!is_file($authFile)) {
    exit('Configuration manquante : copiez config/auth.example.php en config/auth.php.');
}
$credentials = require $authFile;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf($_POST['csrf_token'] ?? null);

    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    // password_verify compare le mot de passe saisi au hash stocké. Le mot de
    // passe en clair n'existe ni dans le code, ni dans le dépôt, ni en base.
    $userOk = hash_equals($credentials['username'], $username);
    $passOk = password_verify($password, $credentials['password_hash']);

    if ($userOk && $passOk) {
        // Nouvel identifiant de session après connexion, pour qu'une session
        // préparée à l'avance par un tiers ne devienne pas une session admin.
        session_regenerate_id(true);
        $_SESSION['user_name'] = 'Admin';
        header('Location: liste.php');
        exit;
    }

    // Une seconde d'attente rend une attaque par dictionnaire bien plus lente
    // sans gêner une personne qui s'est simplement trompée.
    sleep(1);
    $error = 'Identifiants invalides.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Urban Scan Bénin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <h1>Connexion</h1>
            <p>Accédez à votre espace de gestion URBAN SCAN BÉNIN.</p>
            <?php if ($error): ?>
                <div class="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <div style="margin-bottom: 18px; color: rgba(255,255,255,0.75);">
                <a href="utilisateur.php" style="color: var(--accent); text-decoration: underline;">Voir l'espace public</a>
            </div>
            <form method="POST" action="connexion.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required autofocus autocomplete="username">
                <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
        </section>
    </main>
</body>
</html>
