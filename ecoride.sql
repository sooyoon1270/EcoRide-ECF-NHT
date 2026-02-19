-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 19 fév. 2026 à 14:33
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `id_trajet` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `id_destinataire` int(11) NOT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` >= 1 and `note` <= 5),
  `commentaire` text DEFAULT NULL,
  `date_avis` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut_id` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `id_trajet`, `id_expediteur`, `id_destinataire`, `note`, `commentaire`, `date_avis`, `statut_id`) VALUES
(8, 15, 2, 7, 5, 'Incroyable', '2026-02-19 12:01:30', 1),
(11, 15, 2, 7, 5, 'oueoueoue\r\n', '2026-02-19 12:30:46', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `id_trajet` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_reservation` datetime DEFAULT current_timestamp(),
  `statut` varchar(20) DEFAULT 'confirmé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `id_trajet`, `id_utilisateur`, `date_reservation`, `statut`) VALUES
(1, 2, 2, '2026-02-13 15:09:51', 'confirmé'),
(2, 2, 2, '2026-02-13 15:16:33', 'confirmé'),
(3, 5, 2, '2026-02-16 12:13:14', 'confirmé'),
(4, 9, 6, '2026-02-18 23:30:41', 'confirmé'),
(5, 9, 6, '2026-02-18 23:30:46', 'confirmé'),
(6, 10, 6, '2026-02-18 23:33:24', 'confirmé'),
(7, 10, 6, '2026-02-18 23:33:28', 'confirmé'),
(8, 14, 7, '2026-02-19 12:47:51', 'confirmé'),
(9, 15, 2, '2026-02-19 12:59:46', 'confirmé'),
(10, 15, 2, '2026-02-19 13:31:23', 'confirmé'),
(11, 7, 7, '2026-02-19 14:01:11', 'confirmé');

-- --------------------------------------------------------

--
-- Structure de la table `trajets`
--

CREATE TABLE `trajets` (
  `id` int(11) NOT NULL,
  `depart` varchar(100) NOT NULL,
  `arrivee` varchar(100) NOT NULL,
  `date_depart` datetime NOT NULL,
  `prix` decimal(5,0) NOT NULL,
  `places` int(11) NOT NULL,
  `est_electrique` tinyint(4) NOT NULL,
  `duree` time NOT NULL,
  `note_chauffeur` decimal(10,0) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_voiture` int(11) DEFAULT NULL,
  `statut_id` int(11) DEFAULT 1,
  `accepte_animaux` tinyint(1) DEFAULT 0,
  `fumeur_autorise` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trajets`
--

INSERT INTO `trajets` (`id`, `depart`, `arrivee`, `date_depart`, `prix`, `places`, `est_electrique`, `duree`, `note_chauffeur`, `id_utilisateur`, `id_voiture`, `statut_id`, `accepte_animaux`, `fumeur_autorise`) VALUES
(1, 'Paris', 'Lyon', '0000-00-00 00:00:00', 12, 2, 0, '00:00:00', 0, 0, NULL, 1, 0, 0),
(2, 'Paris', 'Marseille', '0000-00-00 00:00:00', 20, 1, 0, '00:00:00', 0, 0, NULL, 1, 0, 0),
(3, 'Paris', 'Lyon', '2026-02-27 11:19:00', 10, 2, 0, '00:00:00', 0, 0, NULL, 1, 0, 0),
(4, 'Paris', 'Pau', '2026-03-12 16:00:00', 20, 2, 0, '00:00:00', 0, 0, NULL, 1, 0, 0),
(5, 'Paris', 'Seoul', '2026-02-17 15:31:00', 2000, 3, 1, '00:00:00', 0, 2, NULL, 3, 0, 0),
(6, 'Paris', 'Pau', '2026-02-18 16:21:00', 2, 2, 0, '00:00:00', 0, 2, NULL, 3, 0, 0),
(7, 'paris', 'Lyon', '2026-02-24 18:25:00', 20, 1, 0, '00:00:00', 0, 2, NULL, 3, 0, 0),
(8, 'Paris', 'Marseille', '2026-02-20 20:00:00', 15, 2, 0, '00:00:00', 0, 2, NULL, 3, 1, 0),
(9, 'Paris', 'Marseille', '2026-02-18 23:02:00', 10, 0, 0, '00:00:00', 0, 2, NULL, 3, 1, 0),
(10, 'paris', 'pau', '2026-02-18 23:29:00', 10, 0, 0, '00:00:00', 0, 2, NULL, 3, 1, 0),
(11, 'Paris', 'Bordeaux', '2026-02-19 12:37:00', 15, 2, 1, '00:00:00', 0, 2, NULL, 1, 1, 1),
(12, 'Paris', 'Pau', '2026-02-19 12:39:00', 15, 2, 1, '00:00:00', 0, 2, NULL, 3, 1, 1),
(13, 'Paris', 'Nante', '2026-02-19 12:41:00', 15, 2, 1, '00:00:00', 0, 2, NULL, 1, 1, 1),
(14, 'Paris', 'Lance', '2026-02-19 12:43:00', 15, 1, 1, '00:00:00', 0, 2, 3, 1, 1, 1),
(15, 'Paris', 'Dijon', '2026-02-19 12:57:00', 20, 0, 1, '00:00:00', 0, 7, 4, 3, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `Role` varchar(100) NOT NULL,
  `credits` int(11) DEFAULT 20
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `Nom`, `Prenom`, `Role`, `credits`) VALUES
(2, '', 'admin@ecoride.fr', '$2y$10$MRwpc8GnHCkN727FSaZB.Owou/4aJBh.eKuo9aq82HN5OGY/2h2Ge', 'Tran', 'Nam Hyu', 'admin', 2),
(4, '', 'jsp@ecoride.fr', '$2y$10$/YqOQKeGW69LpF/K.Vac0eeznuDXDMQm6cy01OBI1sN7gNLTLvp4i', 'Jjeon', 'NamHyu', '', 20),
(6, '', 'user@test.fr', '$2y$10$F1f.cWaB369wivIf4OUOCOGxlJj1FPhJBMQb.yH28M2/4K3gnLn0m', 'jsp', 'jsp', 'utilisateur', 20),
(7, '', 'Nam@ecoride.fr', '$2y$10$4d6bVD/oTq31TxT1idbla.S/vud9OhacZYo5LZ2C1P5uoOsPshCAm', 'JSP', 'Nam', '', 18),
(8, '', 'Fin@ecoride.fr', '$2y$10$clKgzW5CrX.H1/0z58r6J.6/Ned3TkdlU6nQib4RsmXLcRZOYo5hq', 'Sasa', 'FIFI', '', 20);

-- --------------------------------------------------------

--
-- Structure de la table `voitures`
--

CREATE TABLE `voitures` (
  `id` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `energie` varchar(20) NOT NULL,
  `date_circulation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voitures`
--

INSERT INTO `voitures` (`id`, `id_utilisateur`, `marque`, `modele`, `immatriculation`, `energie`, `date_circulation`) VALUES
(1, 2, 'BMW', 'jsp', 'bcp trop de chiffre', 'Electrique', NULL),
(2, 2, 'VOITURE DE FOU', 'jsp', 'TROPDECHIFFRE', 'Electrique', NULL),
(3, 2, 'voiture incroyable', 'lastday', 'BCPDECHIFFRE', 'Electrique', NULL),
(4, 7, 'Renault naze', 'aucune idee', 'Bcppdechiffre', 'Electrique', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trajet` (`id_trajet`),
  ADD KEY `id_expediteur` (`id_expediteur`),
  ADD KEY `id_destinataire` (`id_destinataire`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trajet` (`id_trajet`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `trajets`
--
ALTER TABLE `trajets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `voitures`
--
ALTER TABLE `voitures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_trajet`) REFERENCES `trajets` (`id`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_expediteur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `avis_ibfk_3` FOREIGN KEY (`id_destinataire`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_trajet`) REFERENCES `trajets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD CONSTRAINT `voitures_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
