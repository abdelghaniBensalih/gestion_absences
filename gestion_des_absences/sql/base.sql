CREATE DATABASE gestion_etudiants;
USE gestion_etudiants;

CREATE TABLE filieres(
 id_filiere INT AUTO_INCREMENT PRIMARY KEY,
 nom varchar(255) not null

);

CREATE TABLE etudiants (
 apogee INT PRIMARY KEY ,
 nom varchar(255) not null,
 prenom varchar(255) not null,
 email varchar(255) not null unique ,
 mot_de_passe varchar(255) not null,
 id_filiere INT not null,
 id_module INT not null,
 foreign key (id_filiere) references filieres(id_filiere)
);

CREATE TABLE modules (
   id_module INT AUTO_INCREMENT PRIMARY KEY,
   nom varchar(255) not null,
   nom_responsable varchar(255),
   id_filiere INT not null,
   foreign key (id_filiere) references filieres(id_filiere)
);

CREATE TABLE inscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_etudiant INT not null ,
  id_module INT not null,
  foreign key (id_etudiant) references etudiants(apogee),
  foreign key (id_module) references modules(id_module)
);

CREATE TABLE administrateur (
 id_administrateur INT AUTO_INCREMENT PRIMARY KEY,
 nom varchar(255),
 prenom varchar(255),
 mot_de_passe varchar(255) not null
);