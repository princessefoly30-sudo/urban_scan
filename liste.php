<?php
/**
 * URBAN SCAN BÉNIN - DASHBOARD ADMIN PRO
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('Location: connexion.php');
    exit;
}

$filtreRecherche = trim($_GET['search'] ?? '');
$msg = trim($_GET['msg'] ?? '');

try {
    $requete = "SELECT m.*, p.nom_complet AS proprio, v.nom_ville 
                FROM maison m 
                JOIN proprietaire p ON m.id_proprio = p.id_proprio 
                LEFT JOIN ville v ON m.id_ville = v.id_ville";

    if ($filtreRecherche !== '') {
        $requete .= " WHERE m.nom_residence LIKE :search 
                      OR p.nom_complet LIKE :search 
                      OR v.nom_ville LIKE :search";
    }

    $requete .= " ORDER BY m.id_maison DESC";
    $stmt = $pdo->prepare($requete);

    if ($filtreRecherche !== '') {
        $stmt->execute(['search' => "%{$filtreRecherche}%"]);
    } else {
        $stmt->execute();
    }

    $residences = $stmt->fetchAll();
    $totalResidences = (int) $pdo->query('SELECT COUNT(*) FROM maison')->fetchColumn();
    $totalVilles = (int) $pdo->query('SELECT COUNT(*) FROM ville')->fetchColumn();
} catch (Exception $e) {
    die('Erreur critique : ' . $e->getMessage());
}

$pageTitle = 'Tableau de bord';
$pageSubtitle = 'Vue synthétique et recherche premium des résidences enregistrées.';
$pageAction = null;
$pageActionUrl = '#';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> - Urban Scan Bénin</title>
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

            <?php if ($msg === 'success' || $msg === 'updated' || $msg === 'deleted'): ?>
                <div class="message-banner">
                    <?php if ($msg === 'success'): ?>Enregistrement ajouté avec succès.
                    <?php elseif ($msg === 'updated'): ?>Résidence mise à jour avec succès.
                    <?php else: ?>Résidence supprimée avec succès.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="page-header-action" style="margin-bottom: 24px; gap: 12px; flex-wrap: wrap;">
                <form method="GET" class="search-box" style="min-width: 300px; max-width: 520px; width: 100%;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Rechercher une résidence, propriétaire ou ville..." value="<?= escape($filtreRecherche) ?>">
                </form>
                <?php if ($filtreRecherche !== ''): ?>
                    <a href="liste.php" class="btn btn-secondary">Réinitialiser</a>
                <?php endif; ?>
            </div>

            <div class="stepper" style="margin-bottom: 30px;">
                <div class="step active" data-step="1">
                    <span class="step-circle">1</span>
                    <span class="step-label">Recensement</span>
                </div>
                <div class="step" data-step="2">
                    <span class="step-circle">2</span>
                    <span class="step-label">Sélection</span>
                </div>
                <div class="step" data-step="3">
                    <span class="step-circle">3</span>
                    <span class="step-label">Validation</span>
                </div>
                <div class="step" data-step="4">
                    <span class="step-circle">4</span>
                    <span class="step-label">Publication</span>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Unités enregistrées</div>
                    <div class="value" data-target="<?= (int) $totalResidences ?>">0</div>
                    <div class="note">Nombre total de villas suivies.</div>
                </div>
                <div class="stat-card">
                    <div class="label">Zones couvertes</div>
                    <div class="value" data-target="<?= (int) $totalVilles ?>">0</div>
                    <div class="note">Nombre de villes géolocalisées.</div>
                </div>
                <div class="stat-card">
                    <div class="label">Dernière mise à jour</div>
                    <div class="value" style="font-size:1.5rem; color:#ffffff;" data-target="0"><?= date('d M Y') ?></div>
                    <div class="note">Date du jour affichée en temps réel.</div>
                </div>
            </div>

            <div class="residence-grid">
                <?php if (count($residences) > 0): ?>
                    <?php foreach ($residences as $row): ?>
                        <?php
                        $photo = !empty($row['photo_maison']) ? escape($row['photo_maison']) : 'https://via.placeholder.com/800x440/111111/2ecc71?text=Urban+Scan';
                        $statut = statusLabel(!empty($row['photo_maison']));
                        $statutClasse = statusClass(!empty($row['photo_maison']));
                        $styleArchitectural = escape($row['style_arch'] ?? 'Résidence');
                        $priceLabel = formatPrice($row['prix'] ?? null);
                        ?>
                        <article class="residence-card">
                            <div class="card-img-wrapper">
                                <img src="<?= $photo ?>" class="card-img" alt="<?= htmlspecialchars($row['nom_residence'] ?? 'Résidence') ?>" onerror="this.src='https://via.placeholder.com/800x440/111111/2ecc71?text=Urban+Scan'">
                            </div>
                            <div class="card-info">
                                <div class="badge-row">
                                    <span class="type-badge"><?= $styleArchitectural ?></span>
                                    <span class="status-badge <?= $statutClasse ?>"><?= $statut ?></span>
                                </div>
                                <h3><?= escape($row['nom_residence'] ?? 'Villa Inconnue') ?></h3>
                                <p class="location"><i class="fa-solid fa-map-marker-alt" style="color:var(--accent); margin-right: 6px;"></i><?= escape($row['nom_ville'] ?? 'Bénin') ?></p>
                                <?php if ($priceLabel): ?>
                                    <div class="price-tag" style="margin-bottom: 12px;"><?= $priceLabel ?></div>
                                <?php endif; ?>
                                <div>
                                    <p class="owner-label">Propriétaire</p>
                                    <p class="owner-name"><?= escape($row['proprio'] ?? 'Anonyme') ?></p>
                                </div>
                                <div class="card-actions">
                                    <a href="index.php?edit=<?= $row['id_maison'] ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Éditer</a>
                                    <a href="traitement.php?action=delete&id=<?= $row['id_maison'] ?>" class="btn btn-delete" onclick="return confirm('Attention : Cette action est irréversible. Confirmer ?')"><i class="fa-solid fa-trash-can"></i> Supprimer</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-folder-open fa-3x" style="color: rgba(255,255,255,0.35); margin-bottom: 20px;"></i>
                        <h3>Aucun résultat trouvé</h3>
                        <p>Essayez de modifier vos critères ou ajoutez une nouvelle résidence.</p>
                        <a href="liste.php" class="btn btn-secondary" style="margin-top: 16px;">Réinitialiser la vue</a>
                    </div>
                <?php endif; ?>
            </div>
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>

    <script>
        const counters = document.querySelectorAll('.stat-card .value[data-target]');
        counters.forEach(counter => {
            const target = +counter.dataset.target;
            let current = 0;
            const step = Math.max(1, Math.floor(target / 45));
            const interval = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(interval);
                    return;
                }
                counter.textContent = current;
            }, 18);
        });
    </script>
</body>
</html>
