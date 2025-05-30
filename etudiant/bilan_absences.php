<?php
// filepath: c:\xampp\htdocs\gestion_absences\etudiant\bilan_absences.php
session_start();
if ($_SESSION['auth'] != "Oui") {
    header("Location: ../index.php");
    exit();
}

require_once "../config/db.php";
$title = "Bilan des absences";

// Récupérer l'apogée de l'étudiant connecté
$apogee = isset($_SESSION['apogee']) ? $_SESSION['apogee'] : $_COOKIE['apogee'];

// Récupérer les informations de l'étudiant
try {
    $stmt = $pdo->prepare("SELECT e.nom, e.prenom, f.nom AS nom_filiere 
                          FROM etudiants e 
                          LEFT JOIN filieres f ON e.id_filiere = f.id_filiere 
                          WHERE e.apogee = ?");
    $stmt->execute([$apogee]);
    $etudiantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($etudiantInfo) {
        $nomComplet = $etudiantInfo['nom'] . ' ' . $etudiantInfo['prenom'];
        $filiere = $etudiantInfo['nom_filiere'];
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des informations: " . $e->getMessage();
}
try {
    // Vérifier que l'étudiant a des modules
    $checkModules = $pdo->prepare("SELECT COUNT(*) FROM inscriptions WHERE id_etudiant = ?");
    $checkModules->execute([$apogee]);
    $hasModules = $checkModules->fetchColumn() > 0;
    
    // Vérifier que l'étudiant a des absences
    $checkAbsences = $pdo->prepare("SELECT COUNT(*) FROM absences a JOIN seances s ON a.id_seance = s.id_seance WHERE a.apogee = ?");
    $checkAbsences->execute([$apogee]);
    $hasAbsences = $checkAbsences->fetchColumn() > 0;
    
    // Afficher en commentaire HTML pour le débogage
    echo "<!-- Débogage: Modules inscrits: " . ($hasModules ? "OUI" : "NON") . " -->";
    echo "<!-- Débogage: Absences enregistrées: " . ($hasAbsences ? "OUI" : "NON") . " -->";
    
    if (!$hasAbsences) {
        // Vérifier la structure des tables
        $checkSeances = $pdo->query("SHOW TABLES LIKE 'seances'")->rowCount();
        $checkEtudiants = $pdo->query("SHOW TABLES LIKE 'etudiants'")->rowCount();
        echo "<!-- Débogage: Table seances existe: " . ($checkSeances ? "OUI" : "NON") . " -->";
        echo "<!-- Débogage: Table etudiants existe: " . ($checkEtudiants ? "OUI" : "NON") . " -->";
        
        // Vérifier les valeurs
        echo "<!-- Débogage: Apogée: " . $apogee . " -->";
    }
} catch (PDOException $e) {
    echo "<!-- Erreur de débogage: " . $e->getMessage() . " -->";
}
// Récupérer les absences par module
$absencesParModule = [];
$totalAbsences = 0;
$totalJustifiees = 0;

try {
    // Cette requête suppose que vous avez une table d'absences et de modules
    // Ajustez selon votre structure de base de données
    $stmt = $pdo->prepare(
    "SELECT m.id_module, m.nom AS nom_module, m.code,
            COUNT(a.id_absence) AS total_absences,
            SUM(CASE WHEN a.justifiee = 1 THEN 1 ELSE 0 END) AS absences_justifiees,
            SUM(CASE WHEN a.justifiee = 0 THEN 1 ELSE 0 END) AS absences_non_justifiees
     FROM modules m
     JOIN inscriptions i ON m.id_module = i.id_module
     LEFT JOIN seances s ON m.id_module = s.id_module
     LEFT JOIN absences a ON s.id_seance = a.id_seance AND a.apogee = ?
     WHERE i.id_etudiant = ?
     GROUP BY m.id_module, m.nom, m.code
     ORDER BY m.nom"
);
    $stmt->execute([$apogee, $apogee]);
    $absencesParModule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer les totaux
    foreach ($absencesParModule as $module) {
        $totalAbsences += $module['total_absences'];
        $totalJustifiees += $module['absences_justifiees'];
    }
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des absences: " . $e->getMessage();
}

// Récupérer le détail des absences pour la vue détaillée
$detailAbsences = [];
try {
    $stmt = $pdo->prepare(
    "SELECT a.id_absence, s.date_seance AS date_absence, a.justifiee, a.motif,
            m.nom AS nom_module, m.code,
            s.duree, s.type_seance, s.salle
     FROM absences a
     JOIN seances s ON a.id_seance = s.id_seance
     JOIN modules m ON s.id_module = m.id_module
     WHERE a.apogee = ?
     ORDER BY s.date_seance DESC"
);
    $stmt->execute([$apogee]);
    $detailAbsences = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des détails d'absences: " . $e->getMessage();
}

// Filtres de recherche
$moduleId = isset($_GET['module']) ? intval($_GET['module']) : null;
$justifiee = isset($_GET['justifiee']) ? $_GET['justifiee'] : null;
$dateDebut = isset($_GET['date_debut']) ? $_GET['date_debut'] : null;
$dateFin = isset($_GET['date_fin']) ? $_GET['date_fin'] : null;

// Si un filtre est appliqué, ajuster la requête pour les détails d'absences
if ($moduleId || $justifiee !== null || $dateDebut || $dateFin) {
    $sql = "SELECT a.id_absence, s.date_seance AS date_absence, a.justifiee, a.motif,
                  m.nom AS nom_module, m.code,
                  s.duree, s.type_seance, s.salle
           FROM absences a
           JOIN seances s ON a.id_seance = s.id_seance
           JOIN modules m ON s.id_module = m.id_module
           WHERE a.apogee = ?";
    
    $params = [$apogee];
    
    if ($moduleId) {
        $sql .= " AND m.id_module = ?";
        $params[] = $moduleId;
    }
    
    if ($justifiee !== null) {
        $sql .= " AND a.justifiee = ?";
        $params[] = ($justifiee === "1") ? 1 : 0;
    }
    
    if ($dateDebut) {
        $sql .= " AND a.date_absence >= ?";
        $params[] = $dateDebut;
    }
    
    if ($dateFin) {
        $sql .= " AND a.date_absence <= ?";
        $params[] = $dateFin . ' 23:59:59';
    }
    
    $sql .= " ORDER BY a.date_absence DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $detailAbsences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erreur lors de la filtration des absences: " . $e->getMessage();
    }
}

// Récupérer tous les modules pour le filtre
$modules = [];
try {
    $stmt = $pdo->prepare(
        "SELECT m.id_module, m.nom, m.code
         FROM modules m
         JOIN inscriptions i ON m.id_module = i.id_module
         WHERE i.id_etudiant = ?
         ORDER BY m.nom"
    );
    $stmt->execute([$apogee]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des modules: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - QR-Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #0f3460;
            --secondary-color: #4e8cff;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --dark-color: #1a1a2e;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            color: #fff;
            min-height: 100vh;
            padding-bottom: 3rem;
        }
        
        .container {
            padding: 2rem 1rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .page-title {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0;
            margin-left: 1rem;
        }
        
        .page-title i {
            font-size: 2rem;
            color: var(--info-color);
        }
        
        .student-info {
            background: rgba(59, 130, 246, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid var(--info-color);
        }
        
        .info-group {
            margin-right: 2rem;
        }
        
        .info-label {
            font-size: 0.85rem;
            opacity: 0.7;
            display: block;
            margin-bottom: 0.2rem;
        }
        
        .info-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }
        
        .summary-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .summary-title {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .summary-progress {
            height: 6px;
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .summary-progress-bar {
            height: 100%;
            border-radius: 3px;
        }
        
        .total-absences .summary-icon { color: var(--danger-color); }
        .total-absences .summary-progress-bar { background-color: var(--danger-color); }
        
        .justifiees .summary-icon { color: var(--success-color); }
        .justifiees .summary-progress-bar { background-color: var(--success-color); }
        
        .non-justifiees .summary-icon { color: var(--warning-color); }
        .non-justifiees .summary-progress-bar { background-color: var(--warning-color); }
        
        .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .card-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .card-header h2 i {
            margin-right: 0.7rem;
            color: var(--secondary-color);
        }
        
        .table-container {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 0.5rem;
            overflow: hidden;
        }
        
        .table {
            color: white;
            margin-bottom: 0;
        }
        
        .table thead th {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.2);
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        .table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .absence-module {
            background: rgba(59, 130, 246, 0.2);
            padding: 0.3rem 0.7rem;
            border-radius: 5px;
            font-size: 0.8rem;
            display: inline-block;
            font-weight: 600;
        }
        
        .absence-date {
            display: flex;
            flex-direction: column;
        }
        
        .absence-date span {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        .badge-justified {
            background-color: var(--success-color);
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .badge-not-justified {
            background-color: var(--danger-color);
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .badge-warning {
            background-color: var(--warning-color);
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .badge-info {
            background-color: var(--info-color);
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .filters-container {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .filters-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .filters-title i {
            margin-right: 0.5rem;
            color: var(--info-color);
        }
        
        .filter-form .row {
            align-items: center;
        }
        
        .filter-form label {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-bottom: 0.2rem;
        }
        
        .filter-form select,
        .filter-form input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            color: white;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
        }
        
        .filter-form select:focus,
        .filter-form input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(78, 140, 255, 0.3);
            outline: none;
        }
        
        .filter-form select option {
            background-color: var(--dark-color);
        }
        
        .btn-filter {
            background: linear-gradient(135deg, var(--info-color), var(--secondary-color));
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-filter:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        
        .btn-reset {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .back-button {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
            font-weight: 500;
            width: fit-content;
        }
        
        .back-button i {
            margin-right: 0.5rem;
        }
        
        .back-button:hover {
            color: var(--secondary-color);
            transform: translateX(-5px);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            opacity: 0.6;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .empty-state p {
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .student-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-group {
                margin-bottom: 1rem;
                margin-right: 0;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }
        
        /* Styles pour DataTables */
        .dataTables_wrapper {
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_length select,
        .dataTables_filter input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            color: white;
            padding: 0.2rem 0.5rem;
        }
        
        .dataTables_filter input {
            margin-left: 0.5rem;
        }
        
        .dataTables_info,
        .dataTables_paginate {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .dataTables_paginate .paginate_button {
            padding: 0.3rem 0.8rem;
            margin: 0 0.2rem;
            border-radius: 5px;
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border: none !important;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: var(--secondary-color) !important;
            color: white !important;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        
        .doughnut-chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 250px;
            position: relative;
        }
        select option {
    background-color: var(--dark-color) !important;
    color: white !important;
    padding: 8px !important;
}

/* Styles spécifiques pour les navigateurs basés sur Webkit (Chrome, Safari) */
@media screen and (-webkit-min-device-pixel-ratio:0) {
    select.form-select {
        color-scheme: dark;
    }
    
    select option {
        background-color: var(--dark-color) !important;
    }
}

/* Pour Firefox */
@-moz-document url-prefix() {
    select option {
        background-color: var(--dark-color) !important;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <a href="../dashbord_etudiant.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
        
        <div class="page-title">
            <i class="fas fa-file-alt"></i>
            <h1>Bilan des absences</h1>
        </div>
        
        <!-- Informations de l'étudiant -->
        <div class="student-info">
            <div class="info-group">
                <span class="info-label">Étudiant</span>
                <span class="info-value"><?= htmlspecialchars($nomComplet ?? 'Non disponible') ?></span>
            </div>
            
            <div class="info-group">
                <span class="info-label">Numéro Apogée</span>
                <span class="info-value"><?= htmlspecialchars($apogee) ?></span>
            </div>
            
            <div class="info-group">
                <span class="info-label">Filière</span>
                <span class="info-value"><?= htmlspecialchars($filiere ?? 'Non disponible') ?></span>
            </div>
            
            <div class="info-group">
                <span class="info-label">Année académique</span>
                <span class="info-value">2024-2025</span>
            </div>
        </div>
        
        <!-- Résumé des absences -->
        <div class="summary-cards">
            <div class="summary-card total-absences">
                <i class="fas fa-calendar-times summary-icon"></i>
                <div class="summary-title">Total des absences</div>
                <div class="summary-value"><?= $totalAbsences ?></div>
                <div class="summary-progress">
                    <div class="summary-progress-bar" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="summary-card justifiees">
                <i class="fas fa-check-circle summary-icon"></i>
                <div class="summary-title">Absences justifiées</div>
                <div class="summary-value"><?= $totalJustifiees ?></div>
                <div class="summary-progress">
                    <div class="summary-progress-bar" style="width: <?= $totalAbsences ? ($totalJustifiees / $totalAbsences * 100) : 0 ?>%"></div>
                </div>
            </div>
            
            <div class="summary-card non-justifiees">
                <i class="fas fa-exclamation-triangle summary-icon"></i>
                <div class="summary-title">Absences non justifiées</div>
                <div class="summary-value"><?= $totalAbsences - $totalJustifiees ?></div>
                <div class="summary-progress">
                    <div class="summary-progress-bar" style="width: <?= $totalAbsences ? (($totalAbsences - $totalJustifiees) / $totalAbsences * 100) : 0 ?>%"></div>
                </div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-chart-pie"></i> Répartition des absences</h2>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="doughnut-chart-container">
                        <canvas id="absencesChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="doughnut-chart-container">
                        <canvas id="absencesModuleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-filter"></i> Filtrer les absences</h2>
            </div>
            
            <div class="filters-container">
                <form class="filter-form" action="" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="module">Module</label>
                            <select name="module" id="module" class="form-select">
                                <option value="">Tous les modules</option>
                                <?php foreach ($modules as $module): ?>
                                    <option value="<?= $module['id_module'] ?>" <?= $moduleId == $module['id_module'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($module['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="justifiee">Justification</label>
                            <select name="justifiee" id="justifiee" class="form-select">
                                <option value="">Toutes</option>
                                <option value="1" <?= $justifiee === "1" ? 'selected' : '' ?>>Justifiées</option>
                                <option value="0" <?= $justifiee === "0" ? 'selected' : '' ?>>Non justifiées</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_debut">Date début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= $dateDebut ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_fin">Date fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= $dateFin ?>">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-filter">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="?reset=1" class="btn btn-reset">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tableau des absences par module -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list-alt"></i> Absences par module</h2>
            </div>
            
            <?php if (count($absencesParModule) > 0): ?>
                <div class="table-container">
                    <table class="table table-hover" id="modulesTable">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Code</th>
                                <th class="text-center">Total absences</th>
                                <th class="text-center">Justifiées</th>
                                <th class="text-center">Non justifiées</th>
                                <th class="text-center">Taux d'absentéisme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absencesParModule as $module): ?>
                                <tr>
                                    <td><?= htmlspecialchars($module['nom_module']) ?></td>
                                    <td><?= htmlspecialchars($module['code']) ?></td>
                                    <td class="text-center"><?= $module['total_absences'] ?></td>
                                    <td class="text-center"><?= $module['absences_justifiees'] ?></td>
                                    <td class="text-center"><?= $module['absences_non_justifiees'] ?></td>
                                    <td class="text-center">
                                        <?php 
                                            // Calcul fictif du taux d'absentéisme (à adapter selon vos règles)
                                            $tauxAbsenteisme = min(round(($module['total_absences'] / 20) * 100), 100);
                                            
                                            $badgeClass = 'badge-info';
                                            if ($tauxAbsenteisme > 30) $badgeClass = 'badge-warning';
                                            if ($tauxAbsenteisme > 50) $badgeClass = 'badge-not-justified';
                                        ?>
                                        <span class="<?= $badgeClass ?>"><?= $tauxAbsenteisme ?>%</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Aucune donnée d'absence disponible pour cette filière.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Détail des absences -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-minus"></i> Détail des absences</h2>
            </div>
            
            <?php if (count($detailAbsences) > 0): ?>
                <div class="table-container">
                    <table class="table table-hover" id="absencesTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Module</th>
                                <th>Type de séance</th>
                                <th>Salle</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th>Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detailAbsences as $absence): ?>
                                <tr>
                                    <td class="absence-date">
                                        <?= date('d/m/Y', strtotime($absence['date_absence'])) ?>
                                        <span><?= date('H:i', strtotime($absence['date_absence'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="absence-module"><?= htmlspecialchars($absence['nom_module']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($absence['type_seance']) ?></td>
                                    <td><?= htmlspecialchars($absence['salle']) ?></td>
                                    <td><?= $absence['duree'] ?> min</td>
                                    <td>
                                        <?php if ($absence['justifiee']): ?>
                                            <span class="badge-justified">Justifiée</span>
                                        <?php else: ?>
                                            <span class="badge-not-justified">Non justifiée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $absence['justifiee'] ? htmlspecialchars($absence['motif'] ?? '-') : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p>Aucune absence enregistrée pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialiser DataTables
            $('#modulesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                responsive: true,
                pageLength: 5
            });
            
            $('#absencesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10
            });
            
            // Graphique de répartition justifiées/non justifiées
            const absencesCtx = document.getElementById('absencesChart').getContext('2d');
            new Chart(absencesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Justifiées', 'Non justifiées'],
                    datasets: [{
                        label: 'Absences',
                        data: [
                            <?= $totalJustifiees ?>, 
                            <?= $totalAbsences - $totalJustifiees ?>
                        ],
                        backgroundColor: [
                            '#10b981',
                            '#ef4444'
                        ],
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Répartition des absences',
                            color: '#fff'
                        }
                    }
                }
            });
            
            // Graphique de répartition par module
            const modulesCtx = document.getElementById('absencesModuleChart').getContext('2d');
            new Chart(modulesCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        <?php 
                            $moduleNames = [];
                            $moduleAbsences = [];
                            $moduleColors = [];
                            $colorPalette = ['#4e8cff', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#f97316', '#06b6d4'];
                            
                            foreach ($absencesParModule as $index => $module) {
                                if ($module['total_absences'] > 0) {
                                    $moduleNames[] = "'" . addslashes($module['code']) . "'";
                                    $moduleAbsences[] = $module['total_absences'];
                                    $moduleColors[] = "'" . $colorPalette[$index % count($colorPalette)] . "'";
                                }
                            }
                            echo implode(', ', $moduleNames);
                        ?>
                    ],
                    datasets: [{
                        label: 'Absences par module',
                        data: [<?= implode(', ', $moduleAbsences) ?>],
                        backgroundColor: [<?= implode(', ', $moduleColors) ?>],
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Absences par module',
                            color: '#fff'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>