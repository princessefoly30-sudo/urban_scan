<?php
/**
 * URBAN SCAN BÉNIN - CONFIGURATION DE LA BASE DE DONNÉES
 * Ce fichier assure la liaison entre le moteur PHP et le serveur MySQL.
 */

// Définition des paramètres de connexion
$host     = 'localhost';
$dbname   = 'db_recensement';
$user     = 'root';
$password = ''; // Par défaut vide sur XAMPP/Wamp

try {
    // Création de l'instance PDO avec support UTF-8 complet
   // PDO car gère plusieurs bases de données,offre une protection contre l’injection SQL,permet de récupérer les résultats en tableau associatif facilement.
//À la place de PDO, on pourrait utiliser mysqli,On pourrait utiliser un framework PHP comme Laravel ou Symfony.,On pourrait séparer totalement frontend/backend :
backend PHP ou Node.js pour l’API,
frontend en React/Vue.
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $user, 
        $password,
        [
            // Mode d'erreur : déclenche des exceptions en cas de souci SQL
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Mode de récupération : retourne les données sous forme de tableaux associatifs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Désactive la simulation des requêtes préparées pour plus de sécurité
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // En cas d'échec, on arrête le script proprement avec un message
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
