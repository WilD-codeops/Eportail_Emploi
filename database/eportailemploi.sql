-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 02 jan. 2026 à 16:13
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
  KEY `secteur_id` (`secteur_id`),
  KEY `fk_entreprises_gestionnaire` (`gestionnaire_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `date_inscription`, `gestionnaire_id`, `nom`, `secteur_id`, `adresse`, `code_postal`, `ville`, `pays`, `telephone`, `email`, `siret`, `site_web`, `taille`, `description`, `logo`) VALUES
(1, '2025-12-10 17:50:28', 3, 'TechCorp', 1, '10 rue Tech', '75012', 'Paris', 'France', '0145789632', 'contact@techcorp.fr', '12345678901234', 'https://techcorp.fr', '', NULL, NULL),
(2, '2025-12-10 17:50:28', NULL, 'Clinique SantéPlus', 2, '5 avenue Santé, Lyon', '74100', 'molret', 'france', '0475841236', 'info@santeplus.fr', '98765432109876', 'https://santeplus.fr', '15', 'InfosantePlus est une PME spécialisée dans les solutions numériques pour le secteur de la santé. Leader régional en e-santé, l\'entreprise développe des plateformes SaaS pour la gestion des dossiers médicaux, la téléconsultation et la coordination des soins', NULL);

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
  `auteur_id` int NOT NULL,
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
  KEY `offres_ibfk_2` (`entreprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id`, `auteur_id`, `entreprise_id`, `type_offre_id`, `niveau_qualification_id`, `domaine_emploi_id`, `localisation_id`, `titre`, `description`, `date_debut`, `date_fin`, `duree_contrat`, `salaire`, `statut`) VALUES
(1, 2, 1, 1, 4, 1, 1, 'Développeur PHP', 'Développement d\'applications web...', '2025-06-01', '2025-12-31', 6, 35000.00, 'active');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `profils_candidats`
--

INSERT INTO `profils_candidats` (`id`, `candidat_id`, `poste_recherche`, `description`, `disponibilite`, `mobilite`, `annee_experience`, `niveau_etudes`, `statut_actuel`) VALUES
(1, 1, 'Développeur logiciel', '5 ans en Java et PHP', '2025-07-15', 'France & Europe', 5, 'Bac+5', 'En poste');

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
  UNIQUE KEY `entreprise_id` (`entreprise_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `prenom`, `nom`, `email`, `mot_de_passe`, `telephone`, `role`, `entreprise_id`, `date_creation`, `dernier_acces`) VALUES
(1, 'jean', 'dupont', 'jean.dupont@example.com', '$2y$10$X5YYOYMCaWeMDZjWCQra2.BbCuCdEvjoCBEoo7FuIJJRvf4Cv6Qxu', NULL, 'candidat', NULL, '2025-10-22 14:33:16', NULL),
(2, 'marie', 'durand', 'marie.durand@techcorp.fr', '$2y$10$SKpo7alI7JTrVKhvgVnh9O4BBJELNr.F4mQIlJ5bKhtI0dqbbKVXe', NULL, 'recruteur', NULL, '2025-10-22 14:33:16', NULL),
(3, 'paul', 'martin', 'paul.martin@santeplus.fr', '$2y$10$5RznGtpMk9tMyWAMeFVAIuBLW7Hi8us.hWNJq/JI8.YHoOKQ3hT1O', NULL, 'gestionnaire', 1, '2025-10-22 14:33:16', NULL),
(4, 'wildane', 'madi', 'admin@site.fr', '$2y$10$Oq1N.0rEQPn6iPOPmqhageEapMTcBBWnXr/uYEChbo1uYsHmnM7Wm', NULL, 'admin', NULL, '2025-10-22 14:33:16', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `candidatures_ibfk_3` FOREIGN KEY (`statut_id`) REFERENCES `statuts_candidature` (`id`),
  ADD CONSTRAINT `candidatures_ibfk_4` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `fk_entreprises_gestionnaire` FOREIGN KEY (`gestionnaire_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `experiences_professionnelles`
--
ALTER TABLE `experiences_professionnelles`
  ADD CONSTRAINT `experiences_professionnelles_ibfk_1` FOREIGN KEY (`profil_candidat_id`) REFERENCES `profils_candidats` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `fk_favoris_offre` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `offres_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `offres_ibfk_3` FOREIGN KEY (`type_offre_id`) REFERENCES `types_offres` (`id`),
  ADD CONSTRAINT `offres_ibfk_4` FOREIGN KEY (`niveau_qualification_id`) REFERENCES `niveaux_qualification` (`id`),
  ADD CONSTRAINT `offres_ibfk_5` FOREIGN KEY (`domaine_emploi_id`) REFERENCES `domaines_emploi` (`id`),
  ADD CONSTRAINT `offres_ibfk_6` FOREIGN KEY (`localisation_id`) REFERENCES `localisations` (`id`),
  ADD CONSTRAINT `offres_ibfk_7` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `fk_users_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
