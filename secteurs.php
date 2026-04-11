<?php
require_once __DIR__ . '/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('Location: connexion.php');
    exit;
}
$pageTitle = 'Secteurs';
$pageSubtitle = 'Ajoutez et gérez les villes disponibles.';
$pageAction = null;
$pageActionUrl = '#';
$villes = $pdo->query('SELECT * FROM ville ORDER BY nom_ville ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secteurs - Urban Scan Bénin</title>
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

            <div class="form-card" style="margin-bottom: 24px;">
                <div class="form-header">
                    <div>
                        <h2>Ajouter une ville</h2>
                        <p>Enregistrez une nouvelle zone de recensement pour votre application.</p>
                    </div>
                </div>
                <form action="traitement_ville.php" method="POST">
                    <div class="input-group">
                        <label for="nom_ville">Nom de la ville</label>
                        <input id="nom_ville" name="nom_ville" class="input-field" type="text" placeholder="Cotonou" required>
                    </div>
                    <div class="card-actions" style="justify-content:flex-start;">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>

            <div class="city-grid">
                <?php if (count($villes) > 0): ?>
                    <?php foreach ($villes as $ville): ?>
                        <div class="city-card">
                            <div>
                                <h3><?= htmlspecialchars($ville['nom_ville']) ?></h3>
                                <p>Ville active pour le recensement.</p>
                            </div>
                            <span class="city-chip"><i class="fa-solid fa-location-dot"></i> Active</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="form-card">
                        <h3>Aucune ville enregistrée</h3>
                        <p>Ajoutez une première ville pour commencer la géolocalisation.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>