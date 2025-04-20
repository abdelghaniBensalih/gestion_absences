<?php
try {
  $pdo = new PDO("mysql:host=localhost;dbname=gestion_etudiants;charset=utf8mb4", "root", "password", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}


$sqlEtudiant = "SELECT * from etudiants ";
$lignes = $pdo->query($sqlEtudiant)->fetchAll();

$sqlFiliere = "SELECT * from filieres ";
$lignesF = $pdo->query($sqlFiliere)->fetchAll();

$sqlModule = "SELECT * from modules ";
$lignesM = $pdo->query($sqlModule)->fetchAll();

