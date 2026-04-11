-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 11 avr. 2026 à 22:50
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_recensement`
--

-- --------------------------------------------------------

--
-- Structure de la table `maison`
--

DROP TABLE IF EXISTS `maison`;
CREATE TABLE IF NOT EXISTS `maison` (
  `id_maison` int NOT NULL AUTO_INCREMENT,
  `nom_residence` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `style_arch` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Moderne',
  `photo_maison` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_ville` int DEFAULT NULL,
  `quartier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_maison` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prix` decimal(12,2) DEFAULT NULL,
  `id_proprio` int DEFAULT NULL,
  `date_recensement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maison`),
  KEY `id_ville` (`id_ville`),
  KEY `id_proprio` (`id_proprio`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `maison`
--

INSERT INTO `maison` (`id_maison`, `nom_residence`, `style_arch`, `photo_maison`, `id_ville`, `quartier`, `type_maison`, `prix`, `id_proprio`, `date_recensement`) VALUES
(1, 'Villa Emeraude', 'Moderne', 'uploads/urban_scan_1775811867_c1e8d2b2.jpg', 1, NULL, NULL, NULL, 1, '2026-04-10 09:04:27'),
(5, 'Villa Belle vue', 'Moderne', 'uploads/residences/urban_1775858160_c2b30820.jpg', 1, NULL, NULL, 9999999999.99, 9, '2026-04-10 21:56:00'),
(4, 'Villa Emeraude', 'Moderne', 'uploads/residences/urban_1775858059_6b74b60e.jpg', 1, NULL, NULL, 20500000.00, 8, '2026-04-10 21:54:19');

-- --------------------------------------------------------

--
-- Structure de la table `proprietaire`
--

DROP TABLE IF EXISTS `proprietaire`;
CREATE TABLE IF NOT EXISTS `proprietaire` (
  `id_proprio` int NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profession` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fortune_estimee` bigint DEFAULT NULL,
  PRIMARY KEY (`id_proprio`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `proprietaire`
--

INSERT INTO `proprietaire` (`id_proprio`, `nom_complet`, `profession`, `fortune_estimee`) VALUES
(2, 'FOLY', 'IT Manager', 0),
(3, 'HOUNTON Fred', 'IT Manager', 0),
(4, 'HOUNTON Fred', 'IT Manager', 0),
(5, 'FOLY Princesse', 'IT Manager', 0),
(6, 'FOLY Princesse', 'IT Manager', NULL),
(7, 'HOUNTON Fred', 'IT Manager', NULL),
(8, 'HOUNTON Fred', 'Ingénieur machine learning', NULL),
(9, 'FOLY Princesse', 'IT Manager & Data scientist', NULL),
(10, 'hbgf', 'jhgh', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

DROP TABLE IF EXISTS `ville`;
CREATE TABLE IF NOT EXISTS `ville` (
  `id_ville` int NOT NULL AUTO_INCREMENT,
  `nom_ville` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_ville`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`id_ville`, `nom_ville`) VALUES
(1, 'Cotonou'),
(2, 'Porto-Novo'),
(3, 'Abomey-Calavi'),
(4, 'Ouidah');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
