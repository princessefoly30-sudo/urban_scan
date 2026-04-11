<?php
/**
 * URBAN SCAN BÉNIN - LOGIQUE BACK-END
 */
require_once __DIR__ . '/config/db.php';

// --- SUPPRESSION ---
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    try {
        $id = (int) $_GET['id'];
        $stmt = $pdo->prepare('SELECT photo_maison FROM maison WHERE id_maison = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result && !empty($result['photo_maison']) && file_exists($result['photo_maison'])) {
            @unlink($result['photo_maison']);
        }

        $delete = $pdo->prepare('DELETE FROM maison WHERE id_maison = ?');
        $delete->execute([$id]);

        header('Location: liste.php?msg=deleted');
        exit;
    } catch (Exception $e) {
        die('Erreur lors de la suppression : ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $id_maison = !empty($_POST['id_maison']) ? (int) $_POST['id_maison'] : null;
    $id_proprio = !empty($_POST['id_proprio']) ? (int) $_POST['id_proprio'] : null;

    $nom_complet = trim($_POST['nom_complet'] ?? '');
    $profession = trim($_POST['profession'] ?? '');
    $nom_residence = trim($_POST['nom_residence'] ?? '');
    $id_ville = !empty($_POST['id_ville']) ? (int) $_POST['id_ville'] : null;
    $style_arch = trim($_POST['style_arch'] ?? '') ?: 'Moderne';
    $prixRaw = trim($_POST['prix'] ?? '');
    $prix = $prixRaw !== '' ? str_replace(',', '.', preg_replace('/[^0-9\,\.]/', '', $prixRaw)) : null;
    $image_path = trim($_POST['ancienne_photo'] ?? '');

    if (isset($_FILES['photo_maison']) && $_FILES['photo_maison']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/residences/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['photo_maison']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($extension, $allowed, true)) {
            $filename = 'urban_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photo_maison']['tmp_name'], $destination)) {
                if ($id_maison && !empty($image_path) && file_exists($image_path)) {
                    @unlink($image_path);
                }
                $image_path = 'uploads/residences/' . $filename;
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
    die('Erreur de traitement : ' . $e->getMessage());
}
