<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('Location: connexion.php');
    exit;
}

// --- LOGIQUE DE PRÉREMPLISSAGE POUR L'ÉDITION ---
$id_maison = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$villa = null;

if ($id_maison > 0) {
    $stmt = $pdo->prepare(
        "SELECT m.*, p.nom_complet, p.profession, v.nom_ville 
         FROM maison m 
         JOIN proprietaire p ON m.id_proprio = p.id_proprio 
         LEFT JOIN ville v ON m.id_ville = v.id_ville 
         WHERE m.id_maison = ?"
    );
    $stmt->execute([$id_maison]);
    $villa = $stmt->fetch();
}

$villes = $pdo->query('SELECT id_ville, nom_ville FROM ville ORDER BY nom_ville ASC')->fetchAll();

$pageTitle = $villa ? 'Modifier la résidence' : 'Nouveau recensement';
$pageSubtitle = 'Saisie intelligente et aperçu média en temps réel.';
$pageAction = 'Voir le dashboard';
$pageActionUrl = 'liste.php';
$previewSrc = $villa['photo_maison'] ?? '';
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

            <div class="form-card">
                <div class="form-header">
                    <div>
                        <h2><?= escape($pageTitle) ?></h2>
                        <p><?= escape($pageSubtitle) ?></p>
                    </div>
                    <a href="liste.php" class="btn btn-secondary"><i class="fa-solid fa-chart-line"></i> Retour au dashboard</a>
                </div>

                <div class="stepper">
                    <div class="step active" data-step="1">
                        <span class="step-circle">1</span>
                        <span class="step-label">Titulaire</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-circle">2</span>
                        <span class="step-label">Détails</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-circle">3</span>
                        <span class="step-label">Médias</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-circle">4</span>
                        <span class="step-label">Fin</span>
                    </div>
                </div>

                <form action="traitement.php" method="POST" enctype="multipart/form-data" id="multistep-form">
                    <input type="hidden" name="id_maison" value="<?= $villa['id_maison'] ?? '' ?>">
                    <input type="hidden" name="id_proprio" value="<?= $villa['id_proprio'] ?? '' ?>">
                    <input type="hidden" name="ancienne_photo" value="<?= htmlspecialchars($villa['photo_maison'] ?? '') ?>">

                    <div class="form-step active" id="step1">
                        <div class="input-group">
                            <label for="nom_complet">Nom complet du propriétaire</label>
                            <input type="text" id="nom_complet" name="nom_complet" class="input-field" placeholder="ex: Jean DUPONT" value="<?= escape($villa['nom_complet'] ?? '') ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="profession">Profession</label>
                            <input type="text" id="profession" name="profession" class="input-field" placeholder="ex: Entrepreneur" value="<?= escape($villa['profession'] ?? '') ?>">
                        </div>
                        <div class="card-actions">
                            <span></span>
                            <button type="button" class="btn btn-primary" onclick="validateAndMove(1, 2)">Continuer <i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <div class="input-group">
                            <label for="nom_residence">Nom de la résidence</label>
                            <input type="text" id="nom_residence" name="nom_residence" class="input-field" placeholder="ex: Villa Belle Vue" value="<?= escape($villa['nom_residence'] ?? '') ?>" required>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label for="style_arch">Style architectural</label>
                                <select id="style_arch" name="style_arch" class="input-field">
                                    <?php
                                    $styles = ['Moderne', 'Contemporain', 'Classique', 'Autre'];
                                    foreach ($styles as $style) {
                                        $selected = ($villa['style_arch'] ?? '') === $style ? 'selected' : '';
                                        echo "<option value=\"" . htmlspecialchars($style) . "\" $selected>" . htmlspecialchars($style) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="id_ville">Ville de localisation</label>
                                <select id="id_ville" name="id_ville" class="input-field" required>
                                    <?php foreach ($villes as $v): ?>
                                        <option value="<?= escape($v['id_ville']) ?>" <?= isset($villa['id_ville']) && $villa['id_ville'] == $v['id_ville'] ? 'selected' : '' ?>><?= escape($v['nom_ville']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label for="prix">Prix de la résidence</label>
                                <input type="text" id="prix" name="prix" class="input-field" placeholder="ex: 13 500 000" value="<?= escape($villa['prix'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-secondary" onclick="moveStep(1)"><i class="fa-solid fa-chevron-left"></i> Retour</button>
                            <button type="button" class="btn btn-primary" onclick="validateAndMove(2, 3)">Suivant <i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="form-step" id="step3">
                        <div class="preview-panel">
                            <h3>Aperçu image</h3>
                            <img id="photoPreview" src="<?= $previewSrc ? escape($previewSrc) : '' ?>" alt="Aperçu photo" class="preview-image" style="<?= $previewSrc ? 'display:block;' : '' ?>">
                            <div class="preview-empty" id="previewEmpty" style="<?= $previewSrc ? 'display:none;' : '' ?>">
                                Aucune image sélectionnée.<br>Choisissez une photo pour prévisualiser.
                            </div>
                            <?php if ($previewSrc): ?>
                                <p style="color: rgba(255,255,255,0.7); margin: 0;">Image actuelle : <?= escape(basename($previewSrc)) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <label for="photoMaison">Photo de la propriété</label>
                            <input type="file" id="photoMaison" name="photo_maison" class="input-field" accept="image/*">
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-secondary" onclick="moveStep(2)"><i class="fa-solid fa-chevron-left"></i> Retour</button>
                            <button type="button" class="btn btn-primary" onclick="moveStep(4)">Récapitulatif <i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="form-step" id="step4">
                        <div style="text-align:center; padding: 20px;">
                            <div style="width: 80px; height: 80px; background: rgba(46,204,113,0.12); border-radius: 50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 25px;">
                                <i class="fa-solid fa-check-double fa-2x" style="color: var(--accent);"></i>
                            </div>
                            <h2 style="margin-bottom: 10px;">Prêt pour l'enregistrement</h2>
                            <p style="color: rgba(255,255,255,0.7); line-height: 1.6;">Vérifiez vos informations avant de confirmer.</p>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-secondary" onclick="moveStep(3)">Vérifier</button>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $villa ? 'Confirmer la modification' : 'Finaliser l\'enregistrement' ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
// tout ce js c'est le stepper ,elle m'as permis de diviser le formulaire en  pusieur étapes
    <script
        const steps = document.querySelectorAll('.form-step');
        const nodes = document.querySelectorAll('.step');

        function showStep(stepNumber) {
            steps.forEach(step => step.classList.remove('active'));
            nodes.forEach(node => node.classList.remove('active'));
            const target = document.getElementById('step' + stepNumber);
            if (target) target.classList.add('active');
            nodes.forEach(node => {
                if (parseInt(node.dataset.step, 10) <= stepNumber) {
                    node.classList.add('active');
                }
            });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function moveStep(stepNumber) {
            showStep(stepNumber);
        }

        function validateAndMove(current, next) {
            let valid = true;
            if (current === 1) {
                const value = document.getElementById('nom_complet').value.trim();
                if (!value) {
                    alert('Le nom du propriétaire est obligatoire.');
                    valid = false;
                }
            }
            if (current === 2) {
                const value = document.getElementById('nom_residence').value.trim();
                if (!value) {
                    alert('Le nom de la résidence est obligatoire.');
                    valid = false;
                }
            }
            if (valid) moveStep(next);
        }

        const photoInput = document.getElementById('photoMaison');
        const previewImg = document.getElementById('photoPreview');
        const previewEmpty = document.getElementById('previewEmpty');

        if (photoInput) {
            photoInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                previewImg.src = url;
                previewImg.style.display = 'block';
                previewEmpty.style.display = 'none';
            });
        }
    </script>
    
</body>
</html>
