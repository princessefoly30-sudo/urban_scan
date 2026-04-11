<?php
session_start();
if (isset($_SESSION['user_name'])) {
    header('Location: liste.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $secureUsername = 'admin';
    $securePassword = 'Urb@n$can!B3nin2026';

    if ($username === $secureUsername && $password === $securePassword) {
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['notifications'] = 3;
        header('Location: liste.php');
        exit;
    }

    $error = 'Identifiants invalides. Utilisez le bon nom d’utilisateur et le mot de passe sécurisé.';
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
                <div class="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div style="margin-bottom: 18px; color: rgba(255,255,255,0.75);">
                <a href="utilisateur.php" style="color: var(--accent); text-decoration: underline;">Voir l'espace public</a>
            </div>
            <form method="POST" action="connexion.php">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required autofocus>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            <p class="login-note">Identifiants : <strong>admin</strong> / <strong>Urb@n$can!B3nin2026</strong></p>
        </section>
    </main>
</body>
</html>
