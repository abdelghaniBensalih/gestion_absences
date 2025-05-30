<?php

try {
  $pdo = new PDO("mysql:host=localhost;dbname=gestion_absences;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}


$sqlEtudiant = "SELECT * from etudiants ";
$lignes = $pdo->query($sqlEtudiant)->fetchAll(PDO::FETCH_ASSOC);

$sqlFiliere = "SELECT * from filieres ";
$lignesF = $pdo->query($sqlFiliere)->fetchAll(PDO::FETCH_ASSOC);

$sqlModule = "SELECT * from modules ";
$lignesM = $pdo->query($sqlModule)->fetchAll(PDO::FETCH_ASSOC);

$sqlAdmin = "SELECT * from administrateurs ";
$lignesAd = $pdo->query($sqlAdmin)->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['apogee'])) {
  $sqlDashE = "SELECT * FROM etudiants WHERE apogee = " . $_SESSION['apogee'];
  $lignesDashE = $pdo->query($sqlDashE)->fetchAll(PDO::FETCH_ASSOC);
}
