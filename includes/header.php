<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * En-tête partagé pour URBAN SCAN BÉNIN.
 * Variables attendues : $pageTitle, $pageSubtitle, $pageAction, $pageActionUrl.
 */
$pageTitle = $pageTitle ?? 'Urban Scan Bénin';
$pageSubtitle = $pageSubtitle ?? 'Tableau de bord premium pour la gestion immobilière.';
$pageAction = $pageAction ?? null;
$pageActionUrl = $pageActionUrl ?? '#';
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $_SESSION['user_name'] ?? 'Invité';
$notifications = isset($_SESSION['notifications']) ? (int) $_SESSION['notifications'] : 3;
$authUrl = $isLoggedIn ? 'deconnexion.php' : 'connexion.php';
$authLabel = $isLoggedIn ? 'Déconnexion' : 'Connexion';
?>
<header class="page-header">
    <div class="page-header-left">
        <div class="page-brand">
            <a class="brand-logo" href="liste.php">URBAN<span>SCAN</span></a>
            <span class="brand-tag">Bénin</span>
        </div>
        <div class="page-copy">
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
            <p><?= htmlspecialchars($pageSubtitle) ?></p>
        </div>
    </div>
    <div class="page-header-action">
        <button type="button" class="sidebar-toggle" title="Afficher / masquer le menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <?php if ($pageAction): ?>
            <a href="<?= htmlspecialchars($pageActionUrl) ?>" class="btn btn-secondary"><?= htmlspecialchars($pageAction) ?></a>
        <?php endif; ?>
        <div class="header-tools">
            <a href="#" class="header-notification" title="Notifications">
                <i class="fa-solid fa-bell"></i>
                <span class="notification-count"><?= $notifications ?></span>
            </a>
            <div class="header-user">
                <span class="avatar"><?= htmlspecialchars(strtoupper(substr($userName, 0, 1))) ?></span>
                <div>
                    <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                    <div class="user-role"><?= $isLoggedIn ? 'Administrateur' : 'Visiteur' ?></div>
                </div>
            </div>
            <a href="<?= htmlspecialchars($authUrl) ?>" class="btn <?= $isLoggedIn ? 'btn-secondary' : 'btn-primary' ?>"><?= htmlspecialchars($authLabel) ?></a>
        </div>
    </div>
</header>
//Le header affiche le titre, le bouton de connexion/déconnexion et l’état utilisateur.
Cette séparation 

