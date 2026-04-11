<?php

/**
 * Fonctions utilitaires partagées pour URBAN SCAN BÉNIN.
 */

/**
 * Échappe une valeur pour l'affichage HTML.
 */
function escape(string $value): string // escape() pour sécuriser l’affichage HTML.
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Retourne la classe active du menu en fonction de la page courante.
 */
function isActive(string $page): string //isActive() pour mettre en surbrillance le menu actif.
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}

/**
 * Retourne une classe d'état selon la présence de photo.
 */
function statusClass(bool $hasPhoto): string //statusClass() et statusLabel() pour afficher un état stylé.
{
    return $hasPhoto ? 'published' : 'draft';
}

/**
 * Retourne le libellé d'état selon la présence de photo.
 */
function statusLabel(bool $hasPhoto): string //statusClass() et statusLabel() pour afficher un état stylé.
{
    return $hasPhoto ? 'Publié' : 'Brouillon';
}
 Pour éviter de réécrire du code partout et pour centraliser la sécurité.
/**
 * Formate un prix pour l'affichage en FCFA.
 */
function formatPrice($value): string // formatPrice() pour formater un nombre en 10 000 000 FCFA
{
    if ($value === null || $value === '') {
        return '';
    }
    $number = is_numeric($value) ? (float) $value : 0;
    if ($number <= 0) {
        return '';
    }
    return number_format($number, 0, ',', ' ') . ' FCFA';
}
