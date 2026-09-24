<?php
/**
 * Identifiants de l'espace de gestion.
 *
 * Copiez ce fichier en config/auth.php, qui n'est pas suivi par git, puis
 * remplacez le hash par le vôtre. Le mot de passe n'est jamais stocké en clair.
 *
 * Pour générer un hash :
 *   php -r "echo password_hash('votre-mot-de-passe', PASSWORD_DEFAULT);"
 *
 * Le hash ci-dessous correspond au mot de passe de démonstration
 * « urbanscan2026 ». Changez-le avant toute mise en ligne.
 */
return [
    'username'      => 'admin',
    'password_hash' => '$2y$12$VOMxpj5y1fc30pcUsnt03ubuPuviatbyhoLDur3woKe9TNWNWckx6',
];
