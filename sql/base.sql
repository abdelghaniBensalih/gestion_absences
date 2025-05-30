-- Renommer la base de données pour cohérence
CREATE DATABASE gestion_absences;
USE gestion_absences;

CREATE TABLE filieres (
    id_filiere INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    code VARCHAR(20) UNIQUE
);

CREATE TABLE etudiants (
    apogee INT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL, -- Stocker des hash bcrypt
    id_filiere INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_filiere) REFERENCES filieres(id_filiere)
);

CREATE TABLE administrateurs (
    id_administrateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL, -- Stocker des hash bcrypt
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE modules (
    id_module INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    code VARCHAR(20) UNIQUE,
    nom_responsable VARCHAR(255),
    id_filiere INT NOT NULL,
    semestre VARCHAR(2) NOT NULL,
    FOREIGN KEY (id_filiere) REFERENCES filieres(id_filiere)
);

CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_module INT NOT NULL,
    annee_academique VARCHAR(9) NOT NULL DEFAULT '2024-2025',
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(apogee),
    FOREIGN KEY (id_module) REFERENCES modules(id_module),
    UNIQUE KEY (id_etudiant, id_module, annee_academique)
);

-- Nouvelle table pour les séances de cours
CREATE TABLE seances (
    id_seance INT AUTO_INCREMENT PRIMARY KEY,
    id_module INT NOT NULL,
    date_seance DATETIME NOT NULL,
    duree INT NOT NULL, -- en minutes
    salle VARCHAR(50),
    type_seance ENUM('CM', 'TD', 'TP') NOT NULL,
    FOREIGN KEY (id_module) REFERENCES modules(id_module)
);

CREATE TABLE presences (
    id_presence INT AUTO_INCREMENT PRIMARY KEY,
    id_seance INT NOT NULL,
    apogee INT NOT NULL,
    heure_presence DATETIME NOT NULL,
    mode_presence ENUM('QR', 'Manuel') NOT NULL DEFAULT 'QR',
    FOREIGN KEY (id_seance) REFERENCES seances(id_seance),
    FOREIGN KEY (apogee) REFERENCES etudiants(apogee),
    UNIQUE KEY (id_seance, apogee)
);

CREATE TABLE absences (
    id_absence INT AUTO_INCREMENT PRIMARY KEY,
    id_seance INT NOT NULL,
    apogee INT NOT NULL,
    justifiee BOOLEAN DEFAULT FALSE,
    motif TEXT,
    document_justificatif VARCHAR(255), -- Chemin vers le document
    date_justification DATETIME,
    validee_par INT, -- ID administrateur qui a validé
    FOREIGN KEY (id_seance) REFERENCES seances(id_seance),
    FOREIGN KEY (apogee) REFERENCES etudiants(apogee),
    FOREIGN KEY (validee_par) REFERENCES administrateurs(id_administrateur),
    UNIQUE KEY (id_seance, apogee)
);

-- Table pour l'historique des connexions (sécurité)
CREATE TABLE connexions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('etudiant', 'administrateur') NOT NULL,
    user_id INT NOT NULL,
    date_connexion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    adresse_ip VARCHAR(45) NOT NULL,
    user_agent TEXT
);