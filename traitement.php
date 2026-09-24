<?php
/**
 * URBAN SCAN BÉNIN - LOGIQUE BACK-END
 *
 * Ce fichier écrit et supprime en base. Il est donc réservé aux personnes
 * connectées, et chaque requête doit porter le jeton CSRF de la session.
 */
require_once __DIR__ . '/includes/auth.php';

// Le contrôle d'accès passe avant le chargement de la base : une visiteuse
// non connectée ne doit pas même ouvrir une connexion MySQL.
requireLogin();

const UPLOAD_DIR = 'uploads/residences';

/**
 * Supprime un fichier uniquement s'il se trouve dans le dossier des photos.
 */
function deleteStoredPhoto(?string $relativePath): void
{
    if ($relativePath && pathIsInside($relativePath, UPLOAD_DIR)) {
        @unlink(__DIR__ . '/' . $relativePath);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

requireCsrf($_POST['csrf_token'] ?? null);

// La base n'est ouverte qu'une fois la requête reconnue comme légitime.
require_once __DIR__ . '/config/db.php';

// --- SUPPRESSION ---
// En POST et non plus en GET : un lien visité par erreur ne doit pas effacer
// une fiche, et les navigateurs préchargent parfois les liens.
if (($_POST['action'] ?? '') === 'delete') {
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT photo_maison FROM maison WHERE id_maison = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result) {
            deleteStoredPhoto($result['photo_maison'] ?? null);
        }

        $delete = $pdo->prepare('DELETE FROM maison WHERE id_maison = ?');
        $delete->execute([$id]);

        header('Location: liste.php?msg=deleted');
        exit;
    } catch (Exception $e) {
        error_log('Suppression impossible : ' . $e->getMessage());
        header('Location: liste.php?msg=error');
        exit;
    }
}

try {
    $id_maison  = !empty($_POST['id_maison']) ? (int) $_POST['id_maison'] : null;
    $id_proprio = !empty($_POST['id_proprio']) ? (int) $_POST['id_proprio'] : null;

    $nom_complet    = trim($_POST['nom_complet'] ?? '');
    $profession     = trim($_POST['profession'] ?? '');
    $nom_residence  = trim($_POST['nom_residence'] ?? '');
    $id_ville       = !empty($_POST['id_ville']) ? (int) $_POST['id_ville'] : null;
    $style_arch     = trim($_POST['style_arch'] ?? '') ?: 'Moderne';
    $prixRaw        = trim($_POST['prix'] ?? '');
    $prix           = $prixRaw !== '' ? str_replace(',', '.', preg_replace('/[^0-9\,\.]/', '', $prixRaw)) : null;

    // Le chemin de l'ancienne photo vient du formulaire : on ne le garde que
    // s'il désigne bien un fichier du dossier des photos.
    $ancienne_photo = trim($_POST['ancienne_photo'] ?? '');
    $image_path = pathIsInside($ancienne_photo, UPLOAD_DIR) ? $ancienne_photo : '';

    if (isset($_FILES['photo_maison']) && $_FILES['photo_maison']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/' . UPLOAD_DIR . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $maxBytes = 5 * 1024 * 1024;
        if ($_FILES['photo_maison']['size'] > $maxBytes) {
            header('Location: index.php?msg=too_large');
            exit;
        }

        // On ne fait pas confiance à l'extension envoyée : getimagesize() lit
        // le début du fichier et échoue si ce n'est pas une vraie image.
        $imageInfo = @getimagesize($_FILES['photo_maison']['tmp_name']);
        $allowed = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_GIF  => 'gif',
        ];

        if ($imageInfo !== false && isset($allowed[$imageInfo[2]])) {
            $extension = $allowed[$imageInfo[2]];
            $filename = 'urban_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photo_maison']['tmp_name'], $destination)) {
                if ($id_maison) {
                    deleteStoredPhoto($image_path);
                }
                $image_path = UPLOAD_DIR . '/' . $filename;
            }
        }
    }

    $pdo->beginTransaction();

    if ($id_maison) {
        if ($id_proprio) {
            $updateProprio = $pdo->prepare('UPDATE proprietaire SET nom_complet = ?, profession = ? WHERE id_proprio = ?');
            $updateProprio->execute([$nom_complet, $profession, $id_proprio]);
        } else {
            $insertProprio = $pdo->prepare('INSERT INTO proprietaire (nom_complet, profession) VALUES (?, ?)');
            $insertProprio->execute([$nom_complet, $profession]);
            $id_proprio = (int) $pdo->lastInsertId();
        }

        $updateMaison = $pdo->prepare('UPDATE maison SET nom_residence = ?, style_arch = ?, photo_maison = ?, id_ville = ?, prix = ? WHERE id_maison = ?');
        $updateMaison->execute([$nom_residence, $style_arch, $image_path, $id_ville, $prix, $id_maison]);
        $pdo->commit();
        header('Location: liste.php?msg=updated');
        exit;
    }

    $insertProprio = $pdo->prepare('INSERT INTO proprietaire (nom_complet, profession) VALUES (?, ?)');
    $insertProprio->execute([$nom_complet, $profession]);
    $id_proprio = (int) $pdo->lastInsertId();

    $insertMaison = $pdo->prepare('INSERT INTO maison (nom_residence, style_arch, photo_maison, id_ville, id_proprio, prix) VALUES (?, ?, ?, ?, ?, ?)');
    $insertMaison->execute([$nom_residence, $style_arch, $image_path, $id_ville, $id_proprio, $prix]);

    $pdo->commit();
    header('Location: liste.php?msg=success');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Le détail reste dans les logs : un message d'erreur SQL renvoyé au
    // visiteur décrit la structure de la base à qui veut l'attaquer.
    error_log('Traitement impossible : ' . $e->getMessage());
    header('Location: index.php?msg=error');
    exit;
}
