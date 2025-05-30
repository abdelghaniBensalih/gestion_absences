CREATE DATABASE IF NOT EXISTS gestion_absences CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gestion_absences;

-- Table: absences
CREATE TABLE absences (
  id_absence INT(11) NOT NULL AUTO_INCREMENT,
  id_seance INT(11) NOT NULL,
  apogee INT(11) NOT NULL,
  justifiee TINYINT(1) DEFAULT 0,
  motif TEXT DEFAULT NULL,
  document_justificatif VARCHAR(255) DEFAULT NULL,
  date_justification DATETIME DEFAULT NULL,
  validee_par INT(11) DEFAULT NULL,
  PRIMARY KEY (id_absence),
  UNIQUE KEY unique_absence (apogee, id_seance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: administrateurs
CREATE TABLE `administrateurs` (
  `id_administrateur` int NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: connexions
CREATE TABLE `connexions` (
  `id` int NOT NULL,
  `user_type` enum('etudiant','administrateur') NOT NULL,
  `user_id` int NOT NULL,
  `date_connexion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `adresse_ip` varchar(45) NOT NULL,
  `user_agent` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: etudiants
CREATE TABLE `etudiants` (
  `apogee` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_filiere` int NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: filieres
CREATE TABLE `filieres` (
  `id_filiere` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: inscriptions
CREATE TABLE `inscriptions` (
  `id` int NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_module` int NOT NULL,
  `annee_academique` varchar(9) NOT NULL DEFAULT '2024-2025'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: modules
CREATE TABLE `modules` (
  `id_module` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `nom_responsable` varchar(255) DEFAULT NULL,
  `id_filiere` int NOT NULL,
  `semestre` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: presences
CREATE TABLE `presences` (
  `id_presence` int NOT NULL,
  `id_seance` int NOT NULL,
  `apogee` int NOT NULL,
  `heure_presence` datetime NOT NULL,
  `mode_presence` enum('QR','Manuel') NOT NULL DEFAULT 'QR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: seances
CREATE TABLE `seances` (
  `id_seance` INT NOT NULL AUTO_INCREMENT,
  `id_module` INT NOT NULL,
  `date_seance` DATETIME NOT NULL,
  `duree` INT NOT NULL,
  `salle` VARCHAR(50) DEFAULT NULL,
  `type_seance` ENUM('CM','TD','TP') NOT NULL,
  `absences_generees` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id_seance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
