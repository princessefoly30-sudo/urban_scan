<?php
require_once __DIR__ . '/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('Location: connexion.php');
    exit;
}
$pageTitle = 'Propriétaires';
$pageSubtitle = 'Liste des propriétaires enregistrés.';
$pageAction = null;
$pageActionUrl = '#';
$owners = $pdo->query('SELECT * FROM proprietaire ORDER BY nom_complet ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propriétaires - Urban Scan Bénin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <main class="main-dashboard">
            <?php include __DIR__ . '/includes/header.php'; ?>

            <div class="owner-grid">
                <?php if (count($owners) > 0): ?>
                    <?php foreach ($owners as $owner): ?>
                        <div class="owner-card">
                            <div>
                                <h3><?= htmlspecialchars($owner['nom_complet']) ?></h3>
                                <p><?= htmlspecialchars($owner['profession'] ?? 'Profession non renseignée') ?></p>
                            </div>
                            <a href="index.php?edit=<?= htmlspecialchars($owner['id_proprio']) ?>" class="btn btn-secondary"><i class="fa-solid fa-pen-to-square"></i> Éditer</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="form-card">
                        <h3>Aucun propriétaire trouvé</h3>
                        <p>Ajoutez un propriétaire depuis le formulaire de recensement.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>