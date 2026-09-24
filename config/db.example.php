<?php
/**
 * Connexion à la base de données.
 *
 * Copiez ce fichier en config/db.php, qui n'est pas suivi par git, et adaptez
 * les valeurs. Le script db_recensement.sql, à la racine, crée le schéma attendu.
 */
$host    = 'localhost';
$dbname  = 'db_recensement';
$user    = 'root';
$pass    = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Le détail de l'erreur part dans les logs du serveur, jamais à l'écran :
    // un message PDO brut révèle le nom de la base et celui de l'utilisateur.
    error_log('Connexion base impossible : ' . $e->getMessage());
    http_response_code(500);
    exit('Service momentanément indisponible.');
}
