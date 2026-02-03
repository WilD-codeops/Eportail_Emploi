-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 fév. 2026 à 12:25
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
-- Base de données : `eportailemploi`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

DROP TABLE IF EXISTS `candidatures`;
CREATE TABLE IF NOT EXISTS `candidatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidat_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `statut_id` int NOT NULL,
  `date_postulation` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_id` (`statut_id`),
  KEY `candidatures_ibfk_2` (`offre_id`),
  KEY `candidatures_ibfk_4` (`candidat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `candidat_id`, `offre_id`, `statut_id`, `date_postulation`) VALUES
(1, 1, 1, 1, '2025-05-05');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires_candidatures`
--

DROP TABLE IF EXISTS `commentaires_candidatures`;
CREATE TABLE IF NOT EXISTS `commentaires_candidatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidature_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `commentaire` text NOT NULL,
  `date_commentaire` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `candidature_id` (`candidature_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commentaires_candidatures`
--

INSERT INTO `commentaires_candidatures` (`id`, `candidature_id`, `utilisateur_id`, `commentaire`, `date_commentaire`) VALUES
(1, 1, 2, 'Profil prometteur, organiser entretien.', '2025-05-06 10:15:00');

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

DROP TABLE IF EXISTS `competences`;
CREATE TABLE IF NOT EXISTS `competences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `competences`
--

INSERT INTO `competences` (`id`, `nom`) VALUES
(1, 'Java'),
(2, 'PHP'),
(3, 'SQL');

-- --------------------------------------------------------

--
-- Structure de la table `competences_candidats`
--

DROP TABLE IF EXISTS `competences_candidats`;
CREATE TABLE IF NOT EXISTS `competences_candidats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profil_candidat_id` int NOT NULL,
  `competence_id` int NOT NULL,
  `niveau_maitrise` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_candidat_id` (`profil_candidat_id`),
  KEY `competence_id` (`competence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `competences_candidats`
--

INSERT INTO `competences_candidats` (`id`, `profil_candidat_id`, `competence_id`, `niveau_maitrise`) VALUES
(1, 1, 1, 'Expert'),
(2, 1, 2, 'Intermédiaire');

-- --------------------------------------------------------

--
-- Structure de la table `diplomes`
--

DROP TABLE IF EXISTS `diplomes`;
CREATE TABLE IF NOT EXISTS `diplomes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profil_candidat_id` int NOT NULL,
  `intitule` varchar(150) NOT NULL,
  `annee_obtention` year DEFAULT NULL,
  `etablissement` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diplomes_ibfk_1` (`profil_candidat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `diplomes`
--

INSERT INTO `diplomes` (`id`, `profil_candidat_id`, `intitule`, `annee_obtention`, `etablissement`) VALUES
(1, 1, 'Master Informatique', '2019', 'Université Lyon');

-- --------------------------------------------------------

--
-- Structure de la table `documents_candidats`
--

DROP TABLE IF EXISTS `documents_candidats`;
CREATE TABLE IF NOT EXISTS `documents_candidats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidat_id` int NOT NULL,
  `type_document_id` int NOT NULL,
  `chemin_fichier` varchar(255) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `taille_fichier` int DEFAULT NULL,
  `date_upload` datetime NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `type_document_id` (`type_document_id`),
  KEY `documents_candidats_ibfk_1` (`candidat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `documents_candidats`
--

INSERT INTO `documents_candidats` (`id`, `candidat_id`, `type_document_id`, `chemin_fichier`, `nom_fichier`, `taille_fichier`, `date_upload`, `description`) VALUES
(1, 1, 1, '/uploads/cv_jean.pdf', 'cv_jean.pdf', 125000, '2025-05-01 14:30:00', 'Version mise à jour');

-- --------------------------------------------------------

--
-- Structure de la table `domaines_emploi`
--

DROP TABLE IF EXISTS `domaines_emploi`;
CREATE TABLE IF NOT EXISTS `domaines_emploi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `domaines_emploi`
--

INSERT INTO `domaines_emploi` (`id`, `nom`) VALUES
(3, 'Commerce'),
(1, 'Informatique'),
(2, 'Santé');

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

DROP TABLE IF EXISTS `entreprises`;
CREATE TABLE IF NOT EXISTS `entreprises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gestionnaire_id` int DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `secteur_id` int NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `ville` varchar(150) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `siret` varchar(14) NOT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siret` (`siret`),
  UNIQUE KEY `fk_entreprises_gestionnaire` (`gestionnaire_id`) USING BTREE,
  KEY `secteur_id` (`secteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `date_inscription`, `gestionnaire_id`, `nom`, `secteur_id`, `adresse`, `code_postal`, `ville`, `pays`, `telephone`, `email`, `siret`, `site_web`, `taille`, `description`, `logo`) VALUES
(1, '2025-12-10 17:50:28', 5, 'TechCorp', 1, '10 rue Tech', '75012', 'Paris', 'France', '0475841236', 'techcorp@tech.fr', '12345678901234', '', '', 'TechCorp est une entreprise innovante spécialisée dans le développement de solutions numériques sur mesure. Depuis sa création, elle accompagne les organisations dans leur transformation digitale grâce à une expertise solide en ingénierie logicielle, cybersécurité et infrastructures cloud.\r\nL’entreprise se distingue par une culture centrée sur la qualité, l’agilité et l’innovation continue. Ses équipes pluridisciplinaires conçoivent des applications performantes, sécurisées et évolutives, adaptées aux besoins spécifiques de chaque client.\r\nTechCorp intervient auprès de PME, grands comptes et institutions publiques, en proposant des services allant du conseil technologique à la mise en production de plateformes complexes. Son engagement : offrir des solutions fiables, durables et orientées utilisateur, tout en maintenant un haut niveau d’exigence technique.', NULL),
(3, '2026-01-29 12:57:21', 6, 'Commercia Market', 3, '48 avenue des tilleuls', '91000', 'Evry-Courcouronnes', 'France', '0169874522', 'contact@commercia-market.fr', '90234567800021', 'https://commercia-market.fr', '11-50', 'Commercia Market est une entreprise fictive spécialisée dans la vente de produits alimentaires de proximité. Elle propose une large gamme de produits frais, locaux et artisanaux, tout en développant progressivement une activité de vente en ligne pour faciliter l’accès aux produits du quotidien.', NULL),
(5, '2026-02-03 08:40:24', 14, 'Santé Plus', 2, '45 rue des ormeaux', '91000', 'Evry-Courcouronnes', 'France', '0145789632', 'info@santeplus.fr', '98765432109876', 'https://santeplus.fr', '51-250', 'Santé Plus est un acteur régional reconnu dans le domaine des services médicaux et paramédicaux. \r\nDepuis plus de 15 ans, l’entreprise accompagne les patients, les établissements de soins et les \r\nprofessionnels de santé grâce à une approche centrée sur l’humain, l’innovation et la qualité de service.\r\n\r\nNous proposons des solutions adaptées aux besoins de chacun : assistance médicale à domicile, \r\ncoordination de parcours de santé, accompagnement des personnes âgées ou en situation de handicap, \r\net mise à disposition de matériel médical certifié.\r\n\r\nEngagée dans une démarche d’amélioration continue, Santé Plus investit dans la formation de ses équipes \r\net la modernisation de ses outils pour garantir un suivi fiable, sécurisé et personnalisé.', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `experiences_professionnelles`
--

DROP TABLE IF EXISTS `experiences_professionnelles`;
CREATE TABLE IF NOT EXISTS `experiences_professionnelles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profil_candidat_id` int NOT NULL,
  `poste` varchar(150) NOT NULL,
  `entreprise` varchar(150) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `experiences_professionnelles_ibfk_1` (`profil_candidat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `experiences_professionnelles`
--

INSERT INTO `experiences_professionnelles` (`id`, `profil_candidat_id`, `poste`, `entreprise`, `date_debut`, `date_fin`, `description`) VALUES
(1, 1, 'Développeur Java', 'TechCorp', '2018-01-01', '2023-01-01', 'Développement d\'applications Java.');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `candidat_id` (`user_id`,`offre_id`),
  UNIQUE KEY `unique_favori` (`user_id`,`offre_id`),
  KEY `fk_favoris_offre` (`offre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

DROP TABLE IF EXISTS `formations`;
CREATE TABLE IF NOT EXISTS `formations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_formation` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formations`
--

INSERT INTO `formations` (`id`, `nom_formation`, `description`) VALUES
(1, 'Master Informatique', 'Université Paris-Saclay');

-- --------------------------------------------------------

--
-- Structure de la table `formations_candidats`
--

DROP TABLE IF EXISTS `formations_candidats`;
CREATE TABLE IF NOT EXISTS `formations_candidats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profil_candidat_id` int NOT NULL,
  `formation_id` int NOT NULL,
  `annee_obtention` year DEFAULT NULL,
  `etablissement` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_candidat_id` (`profil_candidat_id`),
  KEY `formation_id` (`formation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formations_candidats`
--

INSERT INTO `formations_candidats` (`id`, `profil_candidat_id`, `formation_id`, `annee_obtention`, `etablissement`) VALUES
(1, 1, 1, '2020', 'Université Paris-Saclay');

-- --------------------------------------------------------

--
-- Structure de la table `historique_candidatures`
--

DROP TABLE IF EXISTS `historique_candidatures`;
CREATE TABLE IF NOT EXISTS `historique_candidatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidature_id` int NOT NULL,
  `statut_id` int NOT NULL,
  `date_modification` datetime NOT NULL,
  `commentaire` text,
  PRIMARY KEY (`id`),
  KEY `candidature_id` (`candidature_id`),
  KEY `statut_id` (`statut_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `historique_candidatures`
--

INSERT INTO `historique_candidatures` (`id`, `candidature_id`, `statut_id`, `date_modification`, `commentaire`) VALUES
(1, 1, 1, '2025-05-05 12:00:00', 'Candidature reçue');

-- --------------------------------------------------------

--
-- Structure de la table `langues`
--

DROP TABLE IF EXISTS `langues`;
CREATE TABLE IF NOT EXISTS `langues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `langues`
--

INSERT INTO `langues` (`id`, `nom`) VALUES
(1, 'Anglais'),
(2, 'Français');

-- --------------------------------------------------------

--
-- Structure de la table `langues_candidats`
--

DROP TABLE IF EXISTS `langues_candidats`;
CREATE TABLE IF NOT EXISTS `langues_candidats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profil_candidat_id` int NOT NULL,
  `langue_id` int NOT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_candidat_id` (`profil_candidat_id`),
  KEY `langue_id` (`langue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `langues_candidats`
--

INSERT INTO `langues_candidats` (`id`, `profil_candidat_id`, `langue_id`, `niveau`) VALUES
(1, 1, 1, 'Courant'),
(2, 1, 2, 'Langue maternelle');

-- --------------------------------------------------------

--
-- Structure de la table `localisations`
--

DROP TABLE IF EXISTS `localisations`;
CREATE TABLE IF NOT EXISTS `localisations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ville` varchar(100) NOT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `pays` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `localisations`
--

INSERT INTO `localisations` (`id`, `ville`, `departement`, `region`, `pays`) VALUES
(1, 'Paris', '75', 'Île-de-France', 'France'),
(2, 'Lyon', '69', 'Auvergne-Rhône-Alpes', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `action` text NOT NULL,
  `date_action` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`id`, `utilisateur_id`, `action`, `date_action`) VALUES
(1, 1, 'Connexion réussie', '2025-04-20 08:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `niveaux_qualification`
--

DROP TABLE IF EXISTS `niveaux_qualification`;
CREATE TABLE IF NOT EXISTS `niveaux_qualification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `niveaux_qualification`
--

INSERT INTO `niveaux_qualification` (`id`, `libelle`) VALUES
(1, 'Bac'),
(2, 'Bac+2'),
(3, 'Bac+3'),
(4, 'Bac+5');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type_notification_id` int NOT NULL,
  `titre` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `lue` tinyint(1) DEFAULT '0',
  `date_envoi` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type_notification_id` (`type_notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type_notification_id`, `titre`, `message`, `lue`, `date_envoi`) VALUES
(1, 1, 1, 'Entretien programmé', 'Votre entretien aura lieu le 10/06/2025', 0, '2025-06-01 08:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `notifications_processus`
--

DROP TABLE IF EXISTS `notifications_processus`;
CREATE TABLE IF NOT EXISTS `notifications_processus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidature_id` int NOT NULL,
  `notification_id` int NOT NULL,
  `statut` enum('envoyé','reçu','lu') DEFAULT 'envoyé',
  `date_traitement` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidature_id` (`candidature_id`),
  KEY `notification_id` (`notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notifications_processus`
--

INSERT INTO `notifications_processus` (`id`, `candidature_id`, `notification_id`, `statut`, `date_traitement`) VALUES
(1, 1, 1, 'envoyé', '2025-06-01 08:00:05');

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

DROP TABLE IF EXISTS `offres`;
CREATE TABLE IF NOT EXISTS `offres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `auteur_id` int DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `modifie_par` int DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  `entreprise_id` int NOT NULL,
  `type_offre_id` int NOT NULL,
  `niveau_qualification_id` int NOT NULL,
  `domaine_emploi_id` int NOT NULL,
  `localisation_id` int NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `duree_contrat` int DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `statut` enum('active','inactive','archive') DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `type_offre_id` (`type_offre_id`),
  KEY `niveau_qualification_id` (`niveau_qualification_id`),
  KEY `domaine_emploi_id` (`domaine_emploi_id`),
  KEY `localisation_id` (`localisation_id`),
  KEY `user_id` (`auteur_id`) USING BTREE,
  KEY `offres_ibfk_2` (`entreprise_id`),
  KEY `fk_offres_modifie_par` (`modifie_par`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id`, `auteur_id`, `date_creation`, `modifie_par`, `date_modification`, `entreprise_id`, `type_offre_id`, `niveau_qualification_id`, `domaine_emploi_id`, `localisation_id`, `titre`, `description`, `date_debut`, `date_fin`, `duree_contrat`, `salaire`, `statut`) VALUES
(1, 2, '0000-00-00 00:00:00', 4, '2026-01-03 19:41:09', 1, 1, 4, 1, 1, 'Développeur PHP', 'Développement d\'applications web...', '2025-06-01', '2025-12-30', 6, 37000.00, 'active'),
(2, 2, '2026-01-04 00:24:41', 5, '2026-02-02 17:15:29', 1, 2, 2, 1, 2, 'Développeur Web Junior PHP / MySQL', 'Nous recherchons un développeur web junior motivé pour rejoindre notre équipe et participer au développement de nouvelles fonctionnalités sur nos applications internes. Vous travaillerez en collaboration avec un développeur senior et un chef de projet.\r\nLes missions incluent :\r\n\r\ndéveloppement PHP natif et MVC\r\n\r\nintégration HTML/CSS\r\n\r\nparticipation aux réunions techniques\r\n\r\nmaintenance corrective et évolutive', '2026-02-02', '2026-09-01', 7, 1400.00, 'active'),
(9, 4, '2026-02-03 09:48:34', NULL, NULL, 5, 1, 3, 2, 1, 'Infirmier(ère) à domicile', 'Vous intervenez au domicile des patients pour assurer les soins courants, \r\nle suivi médical et la coordination avec les équipes soignantes.', '2026-02-20', NULL, NULL, 2300.00, 'active');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_password_resets_user` (`user_id`),
  KEY `idx_password_resets_token` (`token_hash`(250)),
  KEY `idx_password_resets_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 6, 'ea2e750fed1fd503e4d05e4f166f434ebcfe08d6bbd0d203a27816be24036ca4', '2026-01-31 22:40:57', NULL, '2026-01-31 22:40:57'),
(2, 6, 'e292bec3918ad609035d47a2edb2c5ae691dd73386056b04668be66254efa16f', '2026-01-31 23:04:27', NULL, '2026-01-31 23:04:27'),
(3, 6, '4d6e9a11c018aab050d88bff779e725f91abd99ba114a200c67adeb996807837', '2026-01-31 23:27:41', NULL, '2026-01-31 23:27:41'),
(4, 6, '5f8286630c91523a44771fc9a9a8fa1fcc237431c1b7280d21b6d1e763d5a78d', '2026-02-01 00:41:55', '2026-01-31 23:42:14', '2026-01-31 23:41:55'),
(5, 5, '962700899cf1a62f75f9ae51a78183a60609ff7dd7ad72bda343c94ff611e8d2', '2026-02-01 12:16:12', NULL, '2026-02-01 11:16:12'),
(6, 6, 'd7e6296087b55f56b39255263dd990709e87d8d611841f5f017cca40c805138b', '2026-02-01 20:28:51', '2026-02-01 19:30:23', '2026-02-01 19:28:51'),
(7, 6, 'cf3873eb09581fa0e8dddc3adcc00fbb1ecf752b7fa152dfaa99fcc76260fce9', '2026-02-02 12:51:17', '2026-02-02 11:51:51', '2026-02-02 11:51:17'),
(8, 5, '2d088731fb01e38900519313d8ae0a1a5b77e64ad78195528f5924e9b364b5fa', '2026-02-02 14:23:18', '2026-02-02 13:27:00', '2026-02-02 13:23:18'),
(9, 10, '17baf8ddf720412acec5b842be96042b6e6037994acb8028d1b858de9dc22ac0', '2026-02-03 14:09:42', NULL, '2026-02-03 13:09:42');

-- --------------------------------------------------------

--
-- Structure de la table `profils_candidats`
--

DROP TABLE IF EXISTS `profils_candidats`;
CREATE TABLE IF NOT EXISTS `profils_candidats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidat_id` int NOT NULL,
  `poste_recherche` varchar(150) DEFAULT NULL,
  `description` text,
  `disponibilite` date DEFAULT NULL,
  `mobilite` varchar(100) DEFAULT NULL,
  `annee_experience` int DEFAULT NULL,
  `niveau_etudes` varchar(50) DEFAULT NULL,
  `statut_actuel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidat_id` (`candidat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `profils_candidats`
--

INSERT INTO `profils_candidats` (`id`, `candidat_id`, `poste_recherche`, `description`, `disponibilite`, `mobilite`, `annee_experience`, `niveau_etudes`, `statut_actuel`) VALUES
(1, 1, 'Développeur logiciel', '5 ans en Java et PHP', '2025-07-15', 'France & Europe', 5, 'Bac+5', 'En poste'),
(2, 8, 'Developpeur web junnior', 'ACCROCHE PROFESSIONNELLE\r\nDéveloppeur Web Bac+2 RNCP 37273 \"Concepteur Développeur d\'Applications\" \r\nen fin de formation. 1 an d\'expérience en stage backend PHP MVC \r\nsur projet EPortailEmploi (portail emploi modulaire). Maîtrise PHP8, \r\nMySQL 3NF, JavaScript/Ajax, Bootstrap5. Recherche alternance Bac+3/4 \r\npour contribuer à des projets fullstack avec focus backend.', '0000-00-00', 'Paris', 1, 'Bac+2', 'En recherche'),
(3, 9, 'Testeur professionnel', '', '0000-00-00', '', 0, '', ''),
(4, 12, '', '', '0000-00-00', '', 0, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `secteurs_entreprises`
--

DROP TABLE IF EXISTS `secteurs_entreprises`;
CREATE TABLE IF NOT EXISTS `secteurs_entreprises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `secteurs_entreprises`
--

INSERT INTO `secteurs_entreprises` (`id`, `code`, `libelle`, `description`) VALUES
(1, '6201Z', 'Programmation informatique', 'Développement de logiciels, applications informatiques'),
(2, '8610Z', 'Activités hospitalières', 'Soins de santé hospitaliers'),
(3, '4711D', 'Commerce de détail alimentaire', 'Vente de produits alimentaires en magasin');

-- --------------------------------------------------------

--
-- Structure de la table `statuts_candidature`
--

DROP TABLE IF EXISTS `statuts_candidature`;
CREATE TABLE IF NOT EXISTS `statuts_candidature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `statuts_candidature`
--

INSERT INTO `statuts_candidature` (`id`, `code`, `libelle`) VALUES
(1, 'nouveau', 'Nouveau'),
(2, 'entretien', 'Entretien prévu'),
(3, 'refuse', 'Refusé'),
(4, 'recrute', 'Recruté');

-- --------------------------------------------------------

--
-- Structure de la table `taches_processus`
--

DROP TABLE IF EXISTS `taches_processus`;
CREATE TABLE IF NOT EXISTS `taches_processus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidature_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `description` text NOT NULL,
  `statut` enum('ouverte','terminée','en_attente') DEFAULT 'ouverte',
  `date_crea` datetime NOT NULL,
  `date_echeance` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidature_id` (`candidature_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `taches_processus`
--

INSERT INTO `taches_processus` (`id`, `candidature_id`, `utilisateur_id`, `description`, `statut`, `date_crea`, `date_echeance`) VALUES
(1, 1, 2, 'Préparer entretien téléphonique', 'ouverte', '2025-05-07 09:00:00', '2025-05-08 17:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `types_documents`
--

DROP TABLE IF EXISTS `types_documents`;
CREATE TABLE IF NOT EXISTS `types_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_type` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_type` (`nom_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `types_documents`
--

INSERT INTO `types_documents` (`id`, `nom_type`, `description`) VALUES
(1, 'cv', 'Curriculum Vitae'),
(2, 'lettre_motivation', 'Lettre de motivation'),
(3, 'diplome', 'Diplôme ou attestation'),
(4, 'photo_profil', 'Photo de profil');

-- --------------------------------------------------------

--
-- Structure de la table `types_notifications`
--

DROP TABLE IF EXISTS `types_notifications`;
CREATE TABLE IF NOT EXISTS `types_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `types_notifications`
--

INSERT INTO `types_notifications` (`id`, `code`, `description`) VALUES
(1, 'mail', 'Notification par email'),
(2, 'sms', 'Notification SMS'),
(3, 'push', 'Notification push');

-- --------------------------------------------------------

--
-- Structure de la table `types_offres`
--

DROP TABLE IF EXISTS `types_offres`;
CREATE TABLE IF NOT EXISTS `types_offres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `types_offres`
--

INSERT INTO `types_offres` (`id`, `code`, `description`) VALUES
(1, 'cdi', 'Contrat à Durée Indéterminée'),
(2, 'cdd', 'Contrat à Durée Déterminée'),
(3, 'alternance', 'Contrat en alternance');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `role` enum('candidat','recruteur','gestionnaire','admin') NOT NULL,
  `entreprise_id` int DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `dernier_acces` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_entreprise` (`entreprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `prenom`, `nom`, `email`, `mot_de_passe`, `telephone`, `role`, `entreprise_id`, `date_creation`, `dernier_acces`) VALUES
(1, 'jean', 'dupont', 'jean.dupont@exemple.com', '$2y$10$X5YYOYMCaWeMDZjWCQra2.BbCuCdEvjoCBEoo7FuIJJRvf4Cv6Qxu', NULL, 'candidat', NULL, '2025-10-22 14:33:16', '2026-02-03 12:52:33'),
(2, 'marie', 'durand', 'marie.durand@techcorp.fr', '$2y$10$SKpo7alI7JTrVKhvgVnh9O4BBJELNr.F4mQIlJ5bKhtI0dqbbKVXe', NULL, 'recruteur', 1, '2025-10-22 14:33:16', NULL),
(4, 'wildane', 'madi', 'admin@site.fr', '$2y$10$Oq1N.0rEQPn6iPOPmqhageEapMTcBBWnXr/uYEChbo1uYsHmnM7Wm', NULL, 'admin', NULL, '2025-10-22 14:33:16', '2026-02-03 11:37:49'),
(5, 'Lucas', 'Morel', 'lucas.morel@techcorp.fr', '$2y$10$HwA/qkMSD/O/rPKfQEX4jOlYJRAPqE.7J6X3EFl/HBhnBQ1Pr4rO6', '0612458793', 'gestionnaire', 1, '2026-01-04 09:37:08', '2026-02-03 13:07:32'),
(6, 'Sarah', 'Lefevre', 'sarah.lefevre@commercia-market.fr', '$2y$10$sScLW.rkPXG31xIlmTlYLed7a82ijRY8ML9N98SmF21PBWp7YeTye', NULL, 'gestionnaire', 3, '2026-01-29 13:57:21', NULL),
(8, 'Laurent', 'zerno', 'laurent.zerno@gmail.com', '$2y$10$CSCQ0BsdPLTcYl63B6aPRuDDFu8wakudLzTp6HukcShlnTvi8pR1S', NULL, 'candidat', NULL, '2026-01-31 19:31:55', NULL),
(9, 'Candidat', 'Test', 'candidat@test.fr', '$2y$10$Z4vpZW038F1WJfCz8lJhAu.aKW8e.G23Lovjjjtp4LatfxKe.hmNK', NULL, 'candidat', NULL, '2026-02-01 15:18:40', NULL),
(10, 'BigFlo', 'Oli', 'bigflo@oli.fr', '$2y$10$bIkAhc67RAf4vtPwYooaDuyePQftpFU.I6EI5h.Mj2Bm/aBGFh0eK', '0475841236', 'recruteur', 3, '2026-02-02 10:27:54', NULL),
(11, 'admin', 'test', 'admin@test.com', '$2y$10$NmCkRpPUTzJMFYs9ZtlKcOQ7MmbkTuA2ocBuu4U58S4gr595n8q6W', '0698745212', 'admin', 3, '2026-02-02 19:46:10', NULL),
(12, 'julien', 'serbat', 'julien@serbat.fr', '$2y$10$I.4jiMrTesFWgWxNHQanMOSgtIByCDoiKmHSl3uJ73caFNK4ExfNe', '0612457896', 'candidat', NULL, '2026-02-02 20:19:08', NULL),
(13, 'Jean', 'Paul', 'jean@paul.fr', '$2y$10$v9nUNSy8k0MxFBU2OAdDxe9BteJMpvtsLbYIkV1ZRJNo5iI51ghoe', '0632145897', 'recruteur', 1, '2026-02-02 22:44:04', NULL),
(14, 'Claire', 'Santé Plus', 'gestion.santeplus@test.com', '$2y$10$gFQ44F3ayW9zr/XJb6w2vuRHkIRdRyEt6ryiMyf5ZR0uzPJ1/T59a', '0652414512', 'gestionnaire', 5, '2026-02-03 09:40:24', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_3` FOREIGN KEY (`statut_id`) REFERENCES `statuts_candidature` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_4` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commentaires_candidatures`
--
ALTER TABLE `commentaires_candidatures`
  ADD CONSTRAINT `commentaires_candidatures_ibfk_1` FOREIGN KEY (`candidature_id`) REFERENCES `candidatures` (`id`),
  ADD CONSTRAINT `commentaires_candidatures_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `competences_candidats`
--
ALTER TABLE `competences_candidats`
  ADD CONSTRAINT `competences_candidats_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`),
  ADD CONSTRAINT `competences_candidats_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`);

--
-- Contraintes pour la table `diplomes`
--
ALTER TABLE `diplomes`
  ADD CONSTRAINT `diplomes_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `documents_candidats`
--
ALTER TABLE `documents_candidats`
  ADD CONSTRAINT `documents_candidats_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `documents_candidats_ibfk_2` FOREIGN KEY (`type_document_id`) REFERENCES `types_documents` (`id`);

--
-- Contraintes pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD CONSTRAINT `entreprises_ibfk_1` FOREIGN KEY (`secteur_id`) REFERENCES `secteurs_entreprises` (`id`),
  ADD CONSTRAINT `fk_entreprises_gestionnaire` FOREIGN KEY (`gestionnaire_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `experiences_professionnelles`
--
ALTER TABLE `experiences_professionnelles`
  ADD CONSTRAINT `experiences_professionnelles_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `fk_favoris_offre` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoris_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `formations_candidats`
--
ALTER TABLE `formations_candidats`
  ADD CONSTRAINT `formations_candidats_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`),
  ADD CONSTRAINT `formations_candidats_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `historique_candidatures`
--
ALTER TABLE `historique_candidatures`
  ADD CONSTRAINT `historique_candidatures_ibfk_1` FOREIGN KEY (`candidature_id`) REFERENCES `candidatures` (`id`),
  ADD CONSTRAINT `historique_candidatures_ibfk_2` FOREIGN KEY (`statut_id`) REFERENCES `statuts_candidature` (`id`);

--
-- Contraintes pour la table `langues_candidats`
--
ALTER TABLE `langues_candidats`
  ADD CONSTRAINT `langues_candidats_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`),
  ADD CONSTRAINT `langues_candidats_ibfk_2` FOREIGN KEY (`langue_id`) REFERENCES `langues` (`id`);

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`type_notification_id`) REFERENCES `types_notifications` (`id`);

--
-- Contraintes pour la table `notifications_processus`
--
ALTER TABLE `notifications_processus`
  ADD CONSTRAINT `notifications_processus_ibfk_1` FOREIGN KEY (`candidature_id`) REFERENCES `candidatures` (`id`),
  ADD CONSTRAINT `notifications_processus_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`);

--
-- Contraintes pour la table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `fk_offres_modifie_par` FOREIGN KEY (`modifie_par`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_3` FOREIGN KEY (`type_offre_id`) REFERENCES `types_offres` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_4` FOREIGN KEY (`niveau_qualification_id`) REFERENCES `niveaux_qualification` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_5` FOREIGN KEY (`domaine_emploi_id`) REFERENCES `domaines_emploi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_6` FOREIGN KEY (`localisation_id`) REFERENCES `localisations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `offres_ibfk_7` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `profils_candidats`
--
ALTER TABLE `profils_candidats`
  ADD CONSTRAINT `profils_candidats_ibfk_1` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `taches_processus`
--
ALTER TABLE `taches_processus`
  ADD CONSTRAINT `taches_processus_ibfk_1` FOREIGN KEY (`candidature_id`) REFERENCES `candidatures` (`id`),
  ADD CONSTRAINT `taches_processus_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
