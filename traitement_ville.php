<?php
/**
 * Enregistrement d'une ville de recensement.
 *
 * Le formulaire de secteurs.php pointait vers ce fichier, qui n'existait pas :
 * le bouton « Enregistrer » menait à une page 404.
 */
require_once __DIR__ . '/includes/auth.php';

// Le contrôle d'accès passe avant le chargement de la base : une visiteuse
// non connectée ne doit pas même ouvrir une connexion MySQL.
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: secteurs.php');
    exit;
}

requireCsrf($_POST['csrf_token'] ?? null);

require_once __DIR__ . '/config/db.php';

$nom_ville = trim($_POST['nom_ville'] ?? '');
if ($nom_ville === '') {
    header('Location: secteurs.php?msg=empty');
    exit;
}

try {
    // Une ville déjà enregistrée ne doit pas apparaître deux fois dans la liste.
    $existe = $pdo->prepare('SELECT id_ville FROM ville WHERE nom_ville = ?');
    $existe->execute([$nom_ville]);
    if ($existe->fetch()) {
        header('Location: secteurs.php?msg=duplicate');
        exit;
    }

    $insert = $pdo->prepare('INSERT INTO ville (nom_ville) VALUES (?)');
    $insert->execute([$nom_ville]);

    header('Location: secteurs.php?msg=success');
    exit;
} catch (Exception $e) {
    error_log('Ajout de ville impossible : ' . $e->getMessage());
    header('Location: secteurs.php?msg=error');
    exit;
}
