<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$search = trim($_GET['search'] ?? '');

try {
    $sql = "SELECT m.*, p.nom_complet AS proprio, p.profession, v.nom_ville 
            FROM maison m 
            JOIN proprietaire p ON m.id_proprio = p.id_proprio 
            LEFT JOIN ville v ON m.id_ville = v.id_ville";

    if ($search !== '') {
        $sql .= " WHERE m.nom_residence LIKE :search 
                  OR p.nom_complet LIKE :search 
                  OR v.nom_ville LIKE :search";
    }

    $sql .= " ORDER BY m.id_maison DESC";
    $stmt = $pdo->prepare($sql);
    if ($search !== '') {
        $stmt->execute(['search' => "%$search%"]); 
    } else {
        $stmt->execute();
    }
    $houses = $stmt->fetchAll();
} catch (Exception $e) {
    die('Erreur critique : ' . $e->getMessage());
}

$pageTitle = 'Visiteur';
$pageSubtitle = 'Découvrez l’interface publique de votre catalogue de résidences.';
$pageAction = null;
$pageActionUrl = '#';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visiteur - Urban Scan Bénin</title>
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

            <div class="page-header-action" style="margin-bottom: 24px; gap: 12px; flex-wrap: wrap;">
                <form method="GET" class="search-box" style="width: min(100%, 420px); margin-right: auto;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Rechercher une résidence..." value="<?= escape($search) ?>">
                </form>
            </div>

            <div class="residence-grid">
                <?php if (count($houses) > 0): ?>
                    <?php foreach ($houses as $house): ?>
                        <?php
                        $photo = !empty($house['photo_maison']) ? escape($house['photo_maison']) : 'https://via.placeholder.com/800x440/111111/2ecc71?text=Urban+Scan';
                        $type = escape($house['style_arch'] ?? 'Résidence');
                        $price = $house['prix'] ?? $house['prix_maison'] ?? $house['montant'] ?? null;
                        $priceLabel = formatPrice($price);
                        $ownerName = escape($house['proprio'] ?? 'Anonyme');
                        $ownerProfession = escape($house['profession'] ?? 'Profession non renseignée');
                        $propertyName = escape($house['nom_residence'] ?? 'Résidence inconnue');
                        $contactSubject = rawurlencode('Intérêt pour ' . ($house['nom_residence'] ?? 'cette résidence'));
                        $contactLink = 'mailto:contact@urbanscan.bj?subject=' . $contactSubject;
                        ?>
                        <article class="residence-card">
                            <div class="card-img-wrapper">
                                <img src="<?= $photo ?>" class="card-img" alt="<?= escape($house['nom_residence'] ?? 'Résidence') ?>" onerror="this.src='https://via.placeholder.com/800x440/111111/2ecc71?text=Urban+Scan'">
                            </div>
                            <div class="card-info">
                                <div class="badge-row">
                                    <span class="type-badge"><?= $type ?></span>
                                </div>
                                <h3><?= $propertyName ?></h3>
                                <p class="location"><i class="fa-solid fa-map-marker-alt" style="color:var(--accent); margin-right: 6px;"></i><?= escape($house['nom_ville'] ?? 'Ville inconnue') ?></p>
                                <?php if (!empty($house['quartier'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Quartier</span>
                                        <span class="detail-value"><?= escape($house['quartier']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($priceLabel): ?>
                                    <div class="price-tag"><?= $priceLabel ?></div>
                                <?php else: ?>
                                    <div class="price-tag price-unknown">Contactez-nous pour le tarif</div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="detail-label">Propriétaire</span>
                                    <span class="detail-value"><?= $ownerName ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Profession</span>
                                    <span class="detail-value"><?= $ownerProfession ?></span>
                                </div>
                                <div class="card-actions" style="justify-content: flex-start; gap: 12px;">
                                    <a href="<?= $contactLink ?>" class="btn btn-primary"><i class="fa-solid fa-envelope"></i> Contacter</a>
                                    <a href="javascript:;" class="btn btn-secondary"><i class="fa-solid fa-info-circle"></i> En savoir plus</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-folder-open fa-3x" style="color: rgba(255,255,255,0.35); margin-bottom: 20px;"></i>
                        <h3>Aucune résidence trouvée</h3>
                        <p>Essayez une autre recherche ou revenez plus tard.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
</body>
</html>
