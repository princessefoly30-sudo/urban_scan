# Urban Scan Bénin

Application de recensement des résidences, écrite en PHP natif avec PDO et MySQL.

Le projet répond à un besoin simple. Quand on recense des habitations sur le terrain, les informations finissent dispersées entre des carnets, des photos sur un téléphone et des tableurs. Urban Scan rassemble tout au même endroit : la résidence, son propriétaire, sa ville, son style architectural, son prix et sa photo.

## Ce que fait l'application

Une personne habilitée se connecte, puis saisit une résidence depuis un formulaire unique qui crée en même temps la fiche du propriétaire et celle du logement. Elle retrouve ensuite toutes les résidences dans un tableau de bord avec recherche, les modifie, les supprime, gère la liste des villes et consulte des statistiques.

Un espace public, accessible sans connexion, permet de consulter le catalogue des résidences sans pouvoir y toucher.

## Technologies

PHP 8 en procédural, PDO pour l'accès aux données, MySQL, HTML et CSS écrits à la main, sans framework ni dépendance externe. C'était le but : comprendre ce que font les outils avant de les utiliser.

## Installation

Le projet tourne sur un serveur PHP classique, par exemple Wamp ou Laragon.

1. Créez une base de données MySQL, puis importez `db_recensement.sql`.
2. Copiez `config/db.example.php` en `config/db.php` et renseignez vos identifiants de base.
3. Copiez `config/auth.example.php` en `config/auth.php`.
4. Générez le hash de votre mot de passe et collez-le dans ce fichier :

```
php -r "echo password_hash('votre-mot-de-passe', PASSWORD_DEFAULT);"
```

5. Ouvrez `connexion.php` dans votre navigateur.

Les deux fichiers de configuration ne sont pas suivis par git, puisqu'ils contiennent des identifiants.

## Les choix de sécurité

Ce projet m'a surtout servi à comprendre qu'une application ne se protège pas en cachant les adresses de ses pages. Voici ce que j'ai mis en place et pourquoi.

Toutes les requêtes passent par des requêtes préparées PDO. Aucune valeur venue d'un formulaire n'est concaténée dans une chaîne SQL, ce qui ferme la porte aux injections.

Le mot de passe de l'espace de gestion n'existe nulle part en clair. Seul son hash est stocké, dans un fichier de configuration exclu du dépôt, et la vérification se fait avec `password_verify`. L'identifiant de session est régénéré au moment de la connexion, pour qu'une session préparée à l'avance ne puisse pas être réutilisée.

Les pages qui écrivent en base vérifient la session avant toute chose, y compris le fichier de traitement qui n'affiche rien. Une protection qui ne s'applique qu'aux pages visibles ne protège rien.

Chaque écriture porte un jeton CSRF comparé avec `hash_equals`. La suppression se fait en POST et non par un lien, parce qu'un lien peut être visité par erreur ou préchargé par un navigateur.

Les photos envoyées sont vérifiées avec `getimagesize`, qui lit réellement le début du fichier, au lieu de faire confiance à l'extension du nom. Le fichier est renommé aléatoirement et sa taille est limitée à 5 Mo.

Quand l'application supprime une ancienne photo, elle vérifie d'abord avec `realpath` que le chemin reste bien à l'intérieur du dossier des photos. Sans cette vérification, un champ de formulaire modifié pouvait faire supprimer un fichier du projet.

Les messages d'erreur techniques partent dans les logs du serveur. Le visiteur reçoit une phrase neutre, parce qu'un message d'erreur SQL décrit la structure de la base à qui cherche à l'attaquer.

## Organisation du code

```
config/     Connexion à la base et identifiants, hors dépôt
includes/   Session, contrôle d'accès, jetons CSRF, fonctions d'affichage
assets/     Feuille de style
uploads/    Photos des résidences
*.php       Une page par écran, plus les fichiers de traitement
```

Quelques photos de démonstration sont versionnées pour que l'application ne soit pas vide après un clone.

## Ce que je ferais ensuite

Passer d'un compte unique à une vraie table d'utilisateurs avec des rôles, ajouter une pagination sur la liste des résidences, et écrire des tests automatisés. Le projet date de mes premiers mois en PHP et je le garde visible parce qu'il montre d'où je pars.

## Auteure

Princesse FOLY, étudiante en troisième année de Licence en Informatique de gestion à l'UCAO, au Bénin.
