<?php
require_once __DIR__ . '/helpers.php';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="liste.php">URBAN<span>SCAN</span></a>
    </div>
    <nav class="sidebar-menu">
        <a href="liste.php" class="menu-item <?= isActive('liste.php') ?>">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="utilisateur.php" class="menu-item <?= isActive('utilisateur.php') ?>">
            <i class="fa-solid fa-user"></i> Visiteur
        </a>
        <a href="index.php" class="menu-item <?= isActive('index.php') ?>">
            <i class="fa-solid fa-plus-circle"></i> Recensement
        </a>
    </nav>

    <div class="sidebar-settings">
        <div class="settings-title">Paramètres</div>
        <a href="secteurs.php" class="menu-item <?= isActive('secteurs.php') ?>">
            <i class="fa-solid fa-map-location-dot"></i> Gestion des Villes
        </a>
        <a href="proprietaires.php" class="menu-item <?= isActive('proprietaires.php') ?>">
            <i class="fa-solid fa-user-tie"></i> Propriétaires
        </a>
        <a href="parametres.php" class="menu-item <?= isActive('parametres.php') ?>">
            <i class="fa-solid fa-gears"></i> Configuration
        </a>
    </div>

    <div class="sidebar-cta">
        <span><i class="fa-solid fa-shield-halved"></i> Mode Admin Sécurisé</span>
    </div>
</aside>
<div class="sidebar-backdrop"></div>
//Le sidebar contient les liens admin et paramètres.