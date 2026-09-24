<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Urban Scan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --emeraude: #2ecc71; --anthracite: #1a1a1a; --text-muted: #888; }
        body { display: flex; min-height: 100vh; background-color: #0f0f0f; margin: 0; font-family: 'Inter', sans-serif; color: white; }
        
        .sidebar { width: 260px; background-color: var(--anthracite); border-right: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; padding: 30px 0; }
        .menu-item { padding: 16px 30px; display: flex; align-items: center; gap: 15px; color: var(--text-muted); text-decoration: none; transition: 0.3s; border-left: 4px solid transparent; }
        .menu-item.active { background: rgba(46, 204, 113, 0.08); color: var(--emeraude); border-left-color: var(--emeraude); }

        .main-content { flex-grow: 1; padding: 40px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .stat-card { background: var(--anthracite); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); text-align: center; }
        .stat-card i { font-size: 40px; color: var(--emeraude); margin-bottom: 15px; opacity: 0.8; }
        .stat-card h3 { font-size: 14px; text-transform: uppercase; color: var(--text-muted); margin: 0; letter-spacing: 1px; }
        .stat-card p { font-size: 36px; font-weight: 800; margin: 10px 0 0; color: white; }

        .chart-placeholder { background: rgba(255,255,255,0.02); height: 300px; border-radius: 20px; margin-top: 30px; display: flex; align-items: center; justify-content: center; border: 1px dashed #333; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="index.php" style="padding: 0 30px 40px; display: block; color: white; font-weight: 800; text-decoration: none; font-size: 22px;">NVADIGITAL<span>.</span></a>
        <nav>
            <a href="liste.php" class="menu-item"><i class="fa-solid fa-layer-group"></i> Dashboard</a>
            <a href="index.php" class="menu-item"><i class="fa-solid fa-plus-square"></i> Recensement</a>
            <a href="stats.php" class="menu-item active"><i class="fa-solid fa-chart-pie"></i> Statistiques</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1>Analyse des <span>Données</span></h1>
        <p style="color: var(--text-muted);">Aperçu de la croissance du patrimoine immobilier.</p>

        <div class="stats-grid">
            <?php
            // Requêtes pour les compteurs
            $total_maisons = $pdo->query("SELECT COUNT(*) FROM maison")->fetchColumn();
            $total_villes = $pdo->query("SELECT COUNT(*) FROM ville")->fetchColumn();
            $total_proprio = $pdo->query("SELECT COUNT(*) FROM proprietaire")->fetchColumn();
            ?>
            <div class="stat-card">
                <i class="fa-solid fa-house-chimney"></i>
                <h3>Biens Enregistrés</h3>
                <p><?php echo $total_maisons; ?></p>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-city"></i>
                <h3>Villes Couvertes</h3>
                <p><?php echo $total_villes; ?></p>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-users"></i>
                <h3>Propriétaires</h3>
                <p><?php echo $total_proprio; ?></p>
            </div>
        </div>

        <div class="chart-placeholder">
            <p style="color: #444;"><i class="fa-solid fa-chart-line"></i> Graphique de croissance (Bientôt disponible via Chart.js)</p>
        </div>
    </main>
</body>
</html>