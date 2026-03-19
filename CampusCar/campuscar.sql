-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 19 mars 2026 à 13:15
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `campuscar`
--

-- --------------------------------------------------------

--
-- Structure de la table `campus`
--

DROP TABLE IF EXISTS `campus`;
CREATE TABLE IF NOT EXISTS `campus` (
  `id_campus` int NOT NULL AUTO_INCREMENT,
  `nom_campus` varchar(100) NOT NULL,
  `commune` varchar(100) NOT NULL,
  `code_postal` varchar(5) DEFAULT NULL,
  `pole_geographique` varchar(50) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  PRIMARY KEY (`id_campus`),
  UNIQUE KEY `id_campus` (`id_campus`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `campus`
--

INSERT INTO `campus` (`id_campus`, `nom_campus`, `commune`, `code_postal`, `pole_geographique`, `latitude`, `longitude`) VALUES
(1, 'Campus de Schœlcher', 'Schœlcher', '97233', 'Martinique', 14.614316, -61.095700),
(2, 'Campus de Fort-de-France (INSPÉ)', 'Fort-de-France', '97200', 'Martinique', 14.611100, -61.073300),
(3, 'Campus de Fouillole', 'Pointe-à-Pitre', '97110', 'Guadeloupe', 16.241150, -61.531720),
(4, 'Campus du Morne Féret (INSPÉ)', 'Les Abymes', '97139', 'Guadeloupe', 16.262500, -61.534700),
(5, 'Campus du Camp Jacob', 'Saint-Claude', '97120', 'Guadeloupe', 16.030500, -61.698900),
(6, 'IUT de Guadeloupe', 'Saint-Claude', '97120', 'Guadeloupe', 16.028600, -61.701100);

-- --------------------------------------------------------

--
-- Structure de la table `evaluer`
--

DROP TABLE IF EXISTS `evaluer`;
CREATE TABLE IF NOT EXISTS `evaluer` (
  `id_evaluation` int NOT NULL AUTO_INCREMENT,
  `id_trajet` int NOT NULL,
  `id_evaluateur` int NOT NULL,
  `note_etoiles` int DEFAULT NULL,
  `commentaire` text,
  PRIMARY KEY (`id_evaluation`),
  UNIQUE KEY `id_evaluation` (`id_evaluation`),
  KEY `id_trajet` (`id_trajet`),
  KEY `id_evaluateur` (`id_evaluateur`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `evaluer`
--

INSERT INTO `evaluer` (`id_evaluation`, `id_trajet`, `id_evaluateur`, `note_etoiles`, `commentaire`) VALUES
(1, 101, 1114, 5, 'Conduite au top, trajet super agréable et très ponctuel !'),
(2, 101, 1115, 4, 'Super trajet, bonne musique et ambiance cool.'),
(3, 102, 1114, 5, 'Trajet parfait, on m\'a déposée tout près de chez moi, merci !'),
(4, 104, 1112, 5, 'Super ambiance à bord, départ ponctuel et voiture très propre !'),
(5, 104, 1116, 5, 'Très bon trajet, conduite prudente et compagnie au top.'),
(6, 104, 1117, 4, 'Trajet très sympa, tout le monde était à l\'heure au point de rendez-vous.');

-- --------------------------------------------------------

--
-- Structure de la table `profil_conducteur`
--

DROP TABLE IF EXISTS `profil_conducteur`;
CREATE TABLE IF NOT EXISTS `profil_conducteur` (
  `id_profil` int NOT NULL AUTO_INCREMENT,
  `date_permis` date NOT NULL,
  `doc_permis` text,
  `doc_assurance` text,
  `doc_carte_grise` text,
  `id_utilisateur` int NOT NULL,
  `statut_validation` varchar(20) NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (`id_profil`),
  UNIQUE KEY `id_profil` (`id_profil`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=1116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `profil_conducteur`
--

INSERT INTO `profil_conducteur` (`id_profil`, `date_permis`, `doc_permis`, `doc_assurance`, `doc_carte_grise`, `id_utilisateur`, `statut_validation`) VALUES
(1112, '2022-01-11', NULL, NULL, NULL, 1112, 'valide'),
(1113, '2024-05-02', NULL, NULL, NULL, 1115, 'valide'),
(1114, '2025-03-17', NULL, NULL, NULL, 1116, 'en_attente'),
(1115, '2023-02-05', NULL, NULL, NULL, 1117, 'valide');

-- --------------------------------------------------------

--
-- Structure de la table `reserver`
--

DROP TABLE IF EXISTS `reserver`;
CREATE TABLE IF NOT EXISTS `reserver` (
  `id_reservation` int NOT NULL AUTO_INCREMENT,
  `id_passager` int NOT NULL,
  `id_trajet` int NOT NULL,
  `date_resa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_paiement` varchar(50) DEFAULT 'En attente',
  `commission_ua` decimal(4,2) DEFAULT '0.25',
  `bagages` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_reservation`),
  UNIQUE KEY `id_reservation` (`id_reservation`),
  KEY `id_passager` (`id_passager`),
  KEY `id_trajet` (`id_trajet`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reserver`
--

INSERT INTO `reserver` (`id_reservation`, `id_passager`, `id_trajet`, `date_resa`, `statut_paiement`, `commission_ua`, `bagages`) VALUES
(1, 1114, 101, '2026-03-14 21:59:12', 'Payé', 0.25, 0),
(2, 1115, 101, '2026-03-14 21:59:12', 'Payé', 0.25, 1),
(3, 1114, 102, '2026-03-14 21:59:12', 'Payé', 0.25, 0),
(4, 1112, 104, '2026-03-18 02:49:55', 'Payé', 0.25, 0),
(5, 1116, 104, '2026-03-18 02:49:55', 'Payé', 0.25, 1),
(7, 1114, 108, '2026-03-19 00:09:20', 'En attente', 0.25, 0),
(8, 1115, 108, '2026-03-19 00:14:04', 'En attente', 0.25, 0),
(9, 1119, 103, '2026-03-19 00:42:45', 'En attente', 0.25, 0),
(10, 1114, 103, '2026-03-19 02:04:51', 'En attente', 0.25, 0);

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

DROP TABLE IF EXISTS `trajet`;
CREATE TABLE IF NOT EXISTS `trajet` (
  `id_trajet` int NOT NULL AUTO_INCREMENT,
  `date_heure` timestamp NOT NULL,
  `prix_course` decimal(5,2) DEFAULT NULL,
  `places_dispo` int DEFAULT NULL,
  `id_conducteur` int NOT NULL,
  `adresse_exterieure` varchar(150) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `sens_trajet` varchar(20) NOT NULL,
  `id_campus_cible` int NOT NULL,
  PRIMARY KEY (`id_trajet`),
  UNIQUE KEY `id_trajet` (`id_trajet`),
  KEY `id_conducteur` (`id_conducteur`),
  KEY `id_campus_cible` (`id_campus_cible`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `trajet`
--

INSERT INTO `trajet` (`id_trajet`, `date_heure`, `prix_course`, `places_dispo`, `id_conducteur`, `adresse_exterieure`, `latitude`, `longitude`, `sens_trajet`, `id_campus_cible`) VALUES
(1, '2026-03-24 14:30:00', 1.00, 1, 1112, '37 Rue Martin Luther King, Le Lamentin 97232', 14.61606000, -61.00224000, 'vers campus', 1),
(2, '2026-03-24 16:00:00', 3.00, 2, 1115, '73 Rue Schoelcher, Sainte-Luce 97228', 14.46890000, -60.92110000, 'vers campus', 1),
(101, '2026-02-10 12:00:00', 3.00, 0, 1112, '15 Rue des Bougainvilliers, Fort-de-France 97200', NULL, NULL, 'vers campus', 1),
(102, '2026-02-15 21:30:00', 2.00, 2, 1115, 'Quartier Acajou, Le Lamentin 97232', NULL, NULL, 'depuis campus', 1),
(103, '2026-03-24 19:00:00', 3.00, 3, 1117, 'Bourg de Trinité, Trinité 97220', NULL, NULL, 'depuis campus', 1),
(104, '2026-03-15 16:00:00', 2.00, 1, 1117, 'Centre-ville, Case-Pilote 97222', NULL, NULL, 'depuis campus', 2),
(105, '2026-03-24 21:00:00', 2.50, 3, 1115, 'Destreland, Baie-Mahault 97122', NULL, NULL, 'depuis campus', 3),
(108, '2026-03-19 12:30:00', 2.00, 4, 1112, 'Avenue de l’Europe 97222 Case-Pilote', NULL, NULL, 'vers campus', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `id_sesame` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'etudiant',
  `solde_virtuel` decimal(6,2) DEFAULT '10.00',
  `note_moyenne_calc` decimal(3,2) DEFAULT '0.00',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`),
  UNIQUE KEY `id_sesame` (`id_sesame`)
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `id_sesame`, `mot_de_passe`, `nom`, `prenom`, `role`, `solde_virtuel`, `note_moyenne_calc`) VALUES
(1111, 'admin', '$2y$10$G0nwZzlNUqzRLsJ28y9obeLKcdT8T2ej.0KsbDO9qx86jROOv72yC', 'Administrateur1', ' ', 'admin', 10.00, 0.00),
(1112, 'Samira_V972', '$2y$10$Wc9gZO4Lh1q2cb42/qTpzu.ZfLUKpPxJ6.rj6ETeUKngnRUv15N/q', 'Vilar', 'Samira', 'etudiant', 10.00, 0.00),
(1114, 'mbessard06', '$2y$10$0EEZKr3iG7ijV3rHKwA4FeLybZe3.1rTmd6LZDauz.vbO2U06u0QC', 'Bessard', 'Melody', 'etudiant', 15.00, 0.00),
(1115, 'falie06', '$2y$10$mqCa7TBc9CQ2521W/V6TpeRRHYvySWuVdMYloUR9gTRfPP5BSPSNW', 'Alie', 'Flavy', 'etudiant', 20.00, 0.00),
(1116, 'arene06', '$2y$10$smMmmDYOqeil0CHJlyWkqeC2qqUoJZ7ZTZA9VsmrsLrKeelg5YKn6', 'Rene', 'Angelina', 'etudiant', 10.00, 0.00),
(1117, 'edalin06', '$2y$10$OEeeRIDARUlxU0ycNh6/nuNXK7PQZc7CgHLyEe8wmxNxdBp7vUcWO', 'Dalin', 'Evann', 'etudiant', 10.00, 0.00),
(1119, 'adevonin09', '$2y$10$XMS5ToH7pXo.BBRpLaoSgORT1qoJ4bZKKz3.Zs8B7VzqOCrZ.ed/2', 'Devonin', 'Aude', 'etudiant', 10.00, 0.00);
COMMIT;
