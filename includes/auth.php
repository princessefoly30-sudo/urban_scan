<?php

/**
 * Session, contrôle d'accès et jetons CSRF pour URBAN SCAN BÉNIN.
 *
 * Toute page qui écrit en base ou qui affiche des données de gestion doit
 * appeler requireLogin(). Avant, seules les pages d'affichage vérifiaient la
 * session : traitement.php, qui crée et supprime, ne vérifiait rien du tout.
 */

function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['user_name']);
}

/**
 * Coupe la page si la visiteuse n'est pas connectée.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: connexion.php');
        exit;
    }
}

/**
 * Jeton anti-CSRF de la session, créé une fois puis réutilisé.
 *
 * Sans lui, un lien posé sur un autre site suffisait à déclencher une
 * suppression au nom d'une administratrice déjà connectée.
 */
function csrfToken(): string
{
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Refuse la requête si le jeton reçu ne correspond pas à celui de la session.
 */
function requireCsrf(?string $token): void
{
    startSession();
    $expected = $_SESSION['csrf_token'] ?? '';
    if ($expected === '' || !is_string($token) || !hash_equals($expected, $token)) {
        http_response_code(403);
        exit('Requête refusée.');
    }
}

/**
 * Vérifie qu'un chemin de fichier reste bien à l'intérieur du dossier attendu.
 *
 * L'ancien code passait directement au unlink() un chemin venu du formulaire :
 * un champ trafiqué pouvait faire supprimer n'importe quel fichier du projet.
 */
function pathIsInside(string $relativePath, string $allowedDir): bool
{
    if ($relativePath === '') {
        return false;
    }
    $base = realpath(__DIR__ . '/../' . $allowedDir);
    $real = realpath(__DIR__ . '/../' . $relativePath);
    if ($base === false || $real === false) {
        return false;
    }
    return str_starts_with($real, $base . DIRECTORY_SEPARATOR);
}
