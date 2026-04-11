<?php
require_once __DIR__ . '/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('Location: connexion.php');
    exit;
}

$total_villes = $pdo->query('SELECT COUNT(*) FROM ville')->fetchColumn();
$total_proprietaires = $pdo->query('SELECT COUNT(*) FROM proprietaire')->fetchColumn();
$pageTitle = 'Paramètres';
$pageSubtitle = 'Centralisez les réglages, villes et propriétaires depuis un seul espace.';
$pageAction = null;
$pageActionUrl = '#';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Urban Scan Bénin</title>
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

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Villes enregistrées</div>
                    <div class="value"><?= htmlspecialchars((int) $total_villes) ?></div>
                    <div class="note">Toutes les zones disponibles pour le recensement.</div>
                </div>
                <div class="stat-card">
                    <div class="label">Propriétaires</div>
                    <div class="value"><?= htmlspecialchars((int) $total_proprietaires) ?></div>
                    <div class="note">Fiches de propriétaires dynamiques et connectées.</div>
                </div>
                <div class="stat-card">
                    <div class="label">Administration</div>
                    <div class="value">Centralisée</div>
                    <div class="note">Accédez aux villes et propriétaires depuis la barre latérale.</div>
                </div>
            </div>

            <div class="form-card" style="margin-top: 16px;">
                <div class="form-header">
                    <div>
                        <h2>Raccourcis de gestion</h2>
                        <p>Utilisez le menu Paramètres pour accéder aux pages de villes et de propriétaires.</p>
                    </div>
                </div>
                <div class="card-actions" style="justify-content: flex-start; gap: 18px;">
                    <a href="secteurs.php" class="btn btn-secondary"><i class="fa-solid fa-map-location-dot"></i> Gestion des villes</a>
                    <a href="proprietaires.php" class="btn btn-secondary"><i class="fa-solid fa-user-tie"></i> Propriétaires</a>
                    <a href="liste.php" class="btn btn-primary"><i class="fa-solid fa-chart-line"></i> Retour au dashboard</a>
                </div>
            </div>

            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>