<?php
session_start();

if (isset($_POST['qr_data'])) {
    header('Content-Type: application/json');
    if ($_SESSION['auth'] != "Oui") {
        echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
        exit();
    }
    
    require "config/db.php";
    $apogee = isset($_SESSION['apogee']) ? $_SESSION['apogee'] : $_COOKIE['apogee'];
    $qrData = $_POST['qr_data'];
    $response = ['status' => 'error', 'message' => 'QR code invalide'];

    try {
        // Décoder les données du QR code
        $decodedData = base64_decode($qrData);
        $jsonData = json_decode($decodedData, true);
        
        // Vérifier si les données sont valides
        if (!$jsonData || !isset($jsonData['seance_id']) || !isset($jsonData['module_id'])) {
            $response['message'] = 'Format de QR code invalide. Veuillez scanner un QR code de présence valide.';
            echo json_encode($response);
            exit();
        }
        
        $seanceId = $jsonData['seance_id'];
        $moduleId = $jsonData['module_id'];
        
        // Vérifier si la séance existe
        $stmtSeance = $pdo->prepare("SELECT s.id_seance, s.date_seance, s.salle, m.nom AS module_nom 
                                    FROM seances s 
                                    JOIN modules m ON s.id_module = m.id_module 
                                    WHERE s.id_seance = ? AND s.id_module = ?");
        $stmtSeance->execute([$seanceId, $moduleId]);
        $seanceInfo = $stmtSeance->fetch(PDO::FETCH_ASSOC);
        
        if (!$seanceInfo) {
            $response['message'] = 'Séance non trouvée. Veuillez scanner un QR code valide.';
            echo json_encode($response);
            exit();
        }
        
        // Vérifier si l'étudiant est inscrit au module
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM inscriptions WHERE id_etudiant = ? AND id_module = ?");
        $stmtCheck->execute([$apogee, $moduleId]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            $response['message'] = 'Vous n\'êtes pas inscrit à ce module';
            echo json_encode($response);
            exit();
        }
        
        // Vérifier si la présence est déjà enregistrée
        $stmtPresenceCheck = $pdo->prepare("SELECT COUNT(*) FROM presences WHERE apogee = ? AND id_seance = ?");
        $stmtPresenceCheck->execute([$apogee, $seanceId]);
        
        if ($stmtPresenceCheck->fetchColumn() > 0) {
            $response['message'] = 'Votre présence a déjà été enregistrée pour cette séance';
            $response['status'] = 'info';
            echo json_encode($response);
            exit();
        }
        
        // Enregistrer la présence
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO presences (id_seance, apogee, heure_presence, mode_presence) VALUES (?, ?, ?, 'QR')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$seanceId, $apogee, $now]);
        
        $response['status'] = 'success';
        $response['message'] = 'Présence enregistrée avec succès';
        $response['module_name'] = $seanceInfo['module_nom'];
        $response['date'] = date('d/m/Y H:i', strtotime($now));
        $response['salle'] = $seanceInfo['salle'];
    } catch (Exception $e) {
        $response['message'] = 'Une erreur est survenue: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Traitement de l'ancien système de scan par id_module
if (isset($_POST['id_module'])) {
    header('Content-Type: application/json');
    // Reste du code...
    exit();
}

// CONTENU NORMAL DE LA PAGE
if ($_SESSION['auth'] != "Oui") {
    header("Location: index.php");
    exit();
}
require "config/db.php";
$title = "Tableau de bord étudiant";
require_once "includes/header.php";


// Récupérer les informations de l'étudiant
$apogee = isset($_SESSION['apogee']) ? $_SESSION['apogee'] : $_COOKIE['apogee'];
$name = '';
$name2 = '';
$email = '';
$filiere = '';

try {
    $stmt = $pdo->prepare("SELECT e.nom, e.prenom, e.email, e.apogee, f.nom AS nom_filiere 
                          FROM etudiants e 
                          LEFT JOIN filieres f ON e.id_filiere = f.id_filiere 
                          WHERE e.apogee = ?");
    $stmt->execute([$apogee]);
    $etudiantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($etudiantInfo) {
        $name = $etudiantInfo['nom'];
        $name2 = $etudiantInfo['prenom'];
        $email = $etudiantInfo['email'];
        $filiere = $etudiantInfo['nom_filiere'];
    }
} catch (PDOException $e) {
    // Gérer l'erreur silencieusement
}

// Récupérer les modules de l'étudiant
$modules = [];
try {
    $stmt = $pdo->prepare("SELECT m.id_module, m.nom, m.code 
                          FROM inscriptions i 
                          JOIN modules m ON i.id_module = m.id_module 
                          WHERE i.id_etudiant = ?");
    $stmt->execute([$apogee]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer l'erreur silencieusement
}
if (isset($_POST['qr_data'])) {
    // Ne pas afficher d'HTML dans une requête AJAX
    header('Content-Type: application/json');
    
    $qrData = $_POST['qr_data'];
    $response = ['status' => 'error', 'message' => 'QR code invalide'];

    try {
        // Décoder les données du QR code
        $decodedData = base64_decode($qrData);
        $jsonData = json_decode($decodedData, true);
        
        // Vérifier si les données sont valides
        if (!$jsonData || !isset($jsonData['seance_id']) || !isset($jsonData['module_id'])) {
            $response['message'] = 'Format de QR code invalide. Veuillez scanner un QR code de présence valide.';
            echo json_encode($response);
            exit();
        }
        
        $seanceId = $jsonData['seance_id'];
        $moduleId = $jsonData['module_id'];
        
        // Vérifier si la séance existe
        $stmtSeance = $pdo->prepare("SELECT s.id_seance, s.date_seance, s.salle, m.nom AS module_nom 
                                    FROM seances s 
                                    JOIN modules m ON s.id_module = m.id_module 
                                    WHERE s.id_seance = ? AND s.id_module = ?");
        $stmtSeance->execute([$seanceId, $moduleId]);
        $seanceInfo = $stmtSeance->fetch(PDO::FETCH_ASSOC);
        
        if (!$seanceInfo) {
            $response['message'] = 'Séance non trouvée. Veuillez scanner un QR code valide.';
            echo json_encode($response);
            exit();
        }
        
        // Vérifier si l'étudiant est inscrit au module
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM inscriptions WHERE id_etudiant = ? AND id_module = ?");
        $stmtCheck->execute([$apogee, $moduleId]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            $response['message'] = 'Vous n\'êtes pas inscrit à ce module';
            echo json_encode($response);
            exit();
        }
        
        // Vérifier si la présence est déjà enregistrée
        $stmtPresenceCheck = $pdo->prepare("SELECT COUNT(*) FROM presences WHERE apogee = ? AND id_seance = ?");
        $stmtPresenceCheck->execute([$apogee, $seanceId]);
        
        if ($stmtPresenceCheck->fetchColumn() > 0) {
            $response['message'] = 'Votre présence a déjà été enregistrée pour cette séance';
            echo json_encode($response);
            exit();
        }
        
        // Enregistrer la présence
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO presences (id_seance, apogee, heure_presence, mode_presence) VALUES (?, ?, ?, 'QR')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$seanceId, $apogee, $now]);
        
        $response['status'] = 'success';
        $response['message'] = 'Présence enregistrée avec succès';
        $response['module_name'] = $seanceInfo['module_nom'];
        $response['date'] = date('d/m/Y H:i', strtotime($now));
        $response['salle'] = $seanceInfo['salle'];
    } catch (Exception $e) {
        $response['message'] = 'Une erreur est survenue: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}
if (empty($modules)) {
    try {
        // Récupérer l'ID de la filière de l'étudiant
        $stmtFiliere = $pdo->prepare("SELECT id_filiere FROM etudiants WHERE apogee = ?");
        $stmtFiliere->execute([$apogee]);
        $id_filiere = $stmtFiliere->fetchColumn();
        
        if ($id_filiere) {
            // Récupérer les modules de la filière
            $stmtModules = $pdo->prepare("SELECT id_module FROM modules WHERE id_filiere = ?");
            $stmtModules->execute([$id_filiere]);
            $modulesFiliere = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($modulesFiliere) > 0) {
                // Commencer une transaction
                $pdo->beginTransaction();
                
                // Préparer la requête d'insertion
                $sqlInscription = "INSERT INTO inscriptions (id_etudiant, id_module, annee_academique) 
                                  VALUES (?, ?, '2024-2025')";
                $stmtInscription = $pdo->prepare($sqlInscription);
                
                // Inscrire l'étudiant à chaque module
                foreach ($modulesFiliere as $module) {
                    $stmtInscription->execute([$apogee, $module['id_module']]);
                }
                
                // Valider la transaction
                $pdo->commit();
                
                // Recharger la page pour afficher les modules
                echo "<script>window.location.reload();</script>";
                exit();
            }
        }
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Vous pouvez logger l'erreur ou l'afficher
        // echo "Erreur: " . $e->getMessage();
    }
}

// Récupérer les dernières présences de l'étudiant
$presences = [];
try {
    $stmt = $pdo->prepare("SELECT p.heure_presence, m.nom as nom_module 
                          FROM presences p 
                          JOIN seances s ON p.id_seance = s.id_seance
                          JOIN modules m ON s.id_module = m.id_module 
                          WHERE p.apogee = ? 
                          ORDER BY p.heure_presence DESC 
                          LIMIT 5");
    $stmt->execute([$apogee]);
    $presences = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer l'erreur silencieusement
}

// Traiter la soumission du QR code via AJAX
if (isset($_POST['id_module'])) {
    $id_module = filter_var($_POST['id_module'], FILTER_VALIDATE_INT);
    $response = ['status' => 'error', 'message' => 'Erreur inconnue'];

    if ($id_module === false || $id_module <= 0) {
        $response['message'] = 'QR code invalide. Veuillez réessayer.';
        echo json_encode($response);
        exit();
    }

    try {
        // Vérifier si l'étudiant est inscrit au module
        $sqlCheck = "SELECT COUNT(*) FROM inscriptions WHERE id_etudiant = :apogee AND id_module = :id_module";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute(['apogee' => $apogee, 'id_module' => $id_module]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            $response['message'] = 'Vous n\'êtes pas inscrit à ce module';
            echo json_encode($response);
            exit();
        }

        // Vérifier si la présence a déjà été enregistrée pour ce module aujourd'hui
        $today = date('Y-m-d');
        $sqlCheckPresence = "SELECT COUNT(*) FROM presences WHERE apogee = :apogee AND id_module = :id_module AND DATE(date_presence) = :today";
        $stmtPresence = $pdo->prepare($sqlCheckPresence);
        $stmtPresence->execute(['apogee' => $apogee, 'id_module' => $id_module, 'today' => $today]);
        
        if ($stmtPresence->fetchColumn() > 0) {
            $response['message'] = 'Présence déjà enregistrée pour ce module aujourd\'hui';
            echo json_encode($response);
            exit();
        }

        // Récupérer le nom du module pour l'affichage
        $sqlModule = "SELECT nom FROM modules WHERE id_module = :id_module";
        $stmtModule = $pdo->prepare($sqlModule);
        $stmtModule->execute(['id_module' => $id_module]);
        $moduleNom = $stmtModule->fetchColumn();

        // Insérer la présence
        $date_presence = date('Y-m-d H:i:s');
        $sql = "INSERT INTO presences (apogee, id_module, date_presence) VALUES (:apogee, :id_module, :date_presence)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'apogee' => $apogee,
            'id_module' => $id_module,
            'date_presence' => $date_presence
        ]);

        $response['status'] = 'success';
        $response['message'] = 'Présence enregistrée avec succès pour le module: ' . $moduleNom;
        $response['module_name'] = $moduleNom;
        $response['date'] = date('d/m/Y H:i');
    } catch (PDOException $e) {
        $response['message'] = 'Erreur système. Veuillez réessayer ou contacter l\'administrateur.';
    }

    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0f3460;
            --secondary-color: #4e8cff;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --gray-color: #6b7280;
            --dark-color: #1a1a2e;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            color: #fff;
            min-height: 100vh;
            padding-bottom: 3rem;
        }
        
        .dashboard-container {
            padding: 2rem 1rem;
        }
        
        .student-header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.8s ease-out;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .student-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3rem;
            margin-bottom: 1rem;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .student-header h1 {
            font-size: 2rem;
            margin: 0.5rem 0;
            font-weight: 600;
        }
        
        .student-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0.25rem 0;
        }
        
        .student-id {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 80px;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-out;
            animation-fill-mode: both;
            height: 100%;
        }
        
        .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.8rem;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        
        .card-header h2 i {
            margin-right: 0.5rem;
            color: var(--secondary-color);
        }
        
        .qr-section {
            text-align: center;
        }
        
        #scanner-container {
            margin: 1rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        #video-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        #scanner {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 15px;
            border: 2px solid transparent;
            box-sizing: border-box;
            background: linear-gradient(90deg, 
                rgba(78, 140, 255, 0.5) 50%, transparent 50%) 0 0,
                linear-gradient(180deg, 
                rgba(78, 140, 255, 0.5) 50%, transparent 50%) 0 0,
                linear-gradient(270deg, 
                rgba(78, 140, 255, 0.5) 50%, transparent 50%) 100% 0,
                linear-gradient(0deg, 
                rgba(78, 140, 255, 0.5) 50%, transparent 50%) 100% 100%;
            background-repeat: no-repeat;
            background-size: 30px 30px;
            animation: borderBlink 1.5s infinite linear;
        }
        
        @keyframes borderBlink {
            0%, 100% { border-color: rgba(78, 140, 255, 0.8); }
            50% { border-color: rgba(78, 140, 255, 0.3); }
        }
        
        .scan-button {
            padding: 0.8rem 1.5rem;
            border: none;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            font-weight: 500;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin: 0.5rem 0;
            width: 200px;
        }
        
        .scan-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        
        .scan-button:active {
            transform: translateY(-1px);
        }
        
        .scan-button i {
            margin-right: 0.5rem;
        }
        
        #stop-scanner {
            background: linear-gradient(135deg, var(--danger-color), #c53030);
        }
        
        #result {
            padding: 1rem;
            margin-top: 1rem;
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
            border-radius: 5px;
            font-weight: 500;
            text-align: left;
            width: 100%;
            max-width: 300px;
            display: none;
        }
        
        #error {
            padding: 1rem;
            margin-top: 1rem;
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
            background-color: rgba(239, 68, 68, 0.1);
            border-radius: 5px;
            font-weight: 500;
            text-align: left;
            width: 100%;
            max-width: 300px;
            display: none;
        }
        
        .module-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .module-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--secondary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .presence-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .presence-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.8rem;
            margin-bottom: 0.8rem;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
        }
        
        .presence-module {
            font-weight: 600;
        }
        
        .presence-date {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .success-animation {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-out;
            display: none;
        }
        
        .success-icon {
            width: 150px;
            height: 150px;
            background: rgba(16, 185, 129, 0.2);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .check-icon {
            color: var(--success-color);
            font-size: 5rem;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .success-message {
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            font-weight: 600;
            text-align: center;
        }
        
        .success-details {
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .close-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .close-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .account-section {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .account-button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .account-button i {
            margin-right: 0.5rem;
        }
        
        .logout-btn {
            background: var(--gray-color);
        }
        
        .delete-btn {
            background: var(--danger-color);
        }
        
        .account-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Ajouter dans la section des styles */
.info-color {
    background: var(--info-color);
}

.text-center {
    text-align: center;
}

.mt-3 {
    margin-top: 1.5rem;
}

.d-flex {
    display: flex;
}

.justify-content-around {
    justify-content: space-around;
}
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .student-header h1 {
                font-size: 1.5rem;
            }
            
            .account-section {
                flex-direction: column;
                align-items: center;
            }
            
            .account-button {
                width: 100%;
                max-width: 300px;
                justify-content: center;
                position: relative;
            }
        }
        /* Ajuster le style de la section des boutons de compte */
.account-section {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.account-section form {
    margin: 0;
}

/* En version mobile */
@media (max-width: 768px) {
    .account-section {
        flex-direction: column;
        align-items: center;
    }
    
    .account-button {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}
/* Ajouter dans la section style en haut de la page, après le style #error */
#error.info-message {
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
    background-color: rgba(16, 185, 129, 0.1);
}

.error-message {
    color: var(--danger-color);
    border-left: 4px solid var(--danger-color);
    background-color: rgba(239, 68, 68, 0.1);
}
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <!-- En-tête de l'étudiant -->
        <div class="student-header">
            <div class="student-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1><?= htmlspecialchars($name . " " . $name2) ?></h1>
            <p><?= htmlspecialchars($email) ?></p>
            <p><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($filiere) ?></p>
            <div class="student-id">
                <i class="fas fa-id-card"></i> Apogée: <?= htmlspecialchars($apogee) ?>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <!-- Section du Scanner QR -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-qrcode"></i> Scanner de présence</h2>
                </div>
                <div class="qr-section">
                    <p>Scannez le QR code affiché par votre professeur pour enregistrer votre présence au cours.</p>
                    
                    <div id="scanner-container">
                        <div id="video-container" style="display: none;">
                            <video id="scanner" autoplay playsinline></video>
                            <div class="scanner-overlay"></div>
                        </div>
                        <div id="result"></div>
                        <div id="error"></div>
                        <button id="start-scanner" class="scan-button" onclick="startScanner()">
                            <i class="fas fa-camera"></i> Démarrer le scan
                        </button>
                        <button id="stop-scanner" class="scan-button" onclick="stopScanner()" style="display: none;">
                            <i class="fas fa-stop-circle"></i> Arrêter le scan
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Section des informations -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Dernières présences</h2>
                </div>
                
                <?php if (count($presences) > 0): ?>
                    <ul class="presence-list">
                        <?php foreach ($presences as $presence): ?>
                            <li class="presence-item">
                                <div class="presence-module"><?= htmlspecialchars($presence['nom_module']) ?></div>
                                <div class="presence-date">
                                    <i class="far fa-calendar-alt"></i> 
                                    <?= date('d/m/Y à H:i', strtotime($presence['heure_presence'])) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>Aucune présence enregistrée</p>
                    </div>
                <?php endif; ?>
                
                <div class="card-header" style="margin-top: 2rem;">
                    <h2><i class="fas fa-book"></i> Mes modules</h2>
                </div>
                
                <?php if (count($modules) > 0): ?>
                    <ul class="module-list">
                        <?php foreach ($modules as $module): ?>
                            <li class="module-item">
                                <span><?= htmlspecialchars($module['nom']) ?></span>
                                <small><?= htmlspecialchars($module['code']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>Aucun module inscrit</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Section des boutons de compte -->
        <!-- Section des boutons de compte -->
<div class="account-section">
    <!-- Bouton de bilan des absences -->
    <a href="etudiant/bilan_absences.php" class="account-button" style="background: var(--secondary-color);">
        <i class="fas fa-file-alt"></i> Mon bilan d'absences
    </a>
    
    <!-- Bouton de déconnexion -->
    <form action="logout.php" method="post" style="margin: 0;">
        <button type="submit" name="deconnecte" value="deconnecte" class="account-button logout-btn">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
    </form>
    
    <!-- Bouton de suppression de compte -->
    <button type="button" onclick="confirmDeleteAccount()" class="account-button delete-btn">
        <i class="fas fa-user-times"></i> Supprimer mon compte
    </button>
</div>
    </div>
    
    <!-- Animation de succès du scan -->
    <div id="success-animation" class="success-animation">
        <div class="success-icon">
            <i class="fas fa-check-circle check-icon"></i>
        </div>
        <div class="success-message">Présence enregistrée !</div>
        <div class="success-details">
            <div id="success-module"></div>
            <div id="success-date"></div>
        </div>
        <button class="close-button" onclick="hideSuccessAnimation()">Fermer</button>
    </div>

    <script>
        // Variables globales pour le scanner
        let video = document.getElementById("scanner");
        let videoContainer = document.getElementById("video-container");
        let resultDiv = document.getElementById("result");
        let errorDiv = document.getElementById("error");
        let startButton = document.getElementById("start-scanner");
        let stopButton = document.getElementById("stop-scanner");
        let successAnimation = document.getElementById("success-animation");
        let stream = null;
        let scanning = false;
        let scanInterval;

        // Démarrer le scanner de QR code
        function startScanner() {
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: { ideal: "environment" },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            })
            .then(function(mediaStream) {
                stream = mediaStream;
                video.srcObject = stream;
                video.play();
                
                videoContainer.style.display = "block";
                startButton.style.display = "none";
                stopButton.style.display = "block";
                resultDiv.style.display = "none";
                errorDiv.style.display = "none";
                
                scanning = true;
                scanQRCode();
            })
            .catch(function(err) {
                errorDiv.textContent = "Erreur d'accès à la caméra : " + err.message;
                errorDiv.style.display = "block";
            });
        }

        // Arrêter le scanner
        function stopScanner() {
            scanning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
            
            if (scanInterval) {
                clearInterval(scanInterval);
            }
            
            videoContainer.style.display = "none";
            startButton.style.display = "block";
            stopButton.style.display = "none";
        }

        // Fonction pour scanner et traiter le QR code
        // Fonction pour scanner et traiter le QR code
// Fonction pour scanner et traiter le QR code
function scanQRCode() {
    let canvas = document.createElement("canvas");
    let canvasContext = canvas.getContext("2d");
    
    scanInterval = setInterval(() => {
        if (!scanning) {
            clearInterval(scanInterval);
            return;
        }
        
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            let imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            let code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code) {
                // Émettre un son lors de la détection
                beep();
                
                // QR code détecté, envoyer au serveur
                let qrData = code.data;
                
                // Afficher la valeur lue pour le débogage
                console.log("QR Code lu:", qrData);
                
                // Arrêter le scanning pendant le traitement
                clearInterval(scanInterval);
                
                // Créer un formulaire pour envoyer les données
                const formData = new FormData();
                formData.append('qr_data', qrData);
                
                fetch(window.location.href, {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Réponse réseau incorrecte');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Réponse du serveur:", data);
                    if (data.status === "success") {
                        // Afficher l'animation de succès
                        document.getElementById("success-module").textContent = "Module : " + data.module_name;
                        document.getElementById("success-date").textContent = "Date : " + data.date;
                        if (data.salle) {
                            document.getElementById("success-salle").textContent = "Salle : " + data.salle;
                        }
                        successAnimation.style.display = "flex";
                        
                        // Arrêter le scanner
                        stopScanner();
                    }
                    else if (data.status === "info") {
        // Afficher le message informatif en vert
        errorDiv.textContent = data.message;
        errorDiv.style.display = "block";
        errorDiv.className = "info-message"; // Appliquer une classe différente
        
        // Reprendre le scanning après 3 secondes
        setTimeout(() => {
            if (scanning) {
                scanQRCode();
            }
        }, 3000);
    }  else {
                        // Afficher l'erreur
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = "block";
                        
                        // Reprendre le scanning après 3 secondes
                        setTimeout(() => {
                            if (scanning) {
                                scanQRCode();
                            }
                        }, 3000);
                    }
                })
                .catch(err => {
                    console.error("Erreur:", err);
                    errorDiv.textContent = "Erreur de communication avec le serveur: " + err.message;
                    errorDiv.style.display = "block";
                    
                    // Reprendre le scanning après 3 secondes
                    setTimeout(() => {
                        if (scanning) {
                            scanQRCode();
                        }
                    }, 3000);
                });
            }
        }
    }, 100);
}
        
        // Fonction pour cacher l'animation de succès
        function hideSuccessAnimation() {
            successAnimation.style.display = "none";
        }
        
        // Fonction pour émettre un son lors de la détection du QR code
        function beep() {
            let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            let oscillator = audioCtx.createOscillator();
            let gainNode = audioCtx.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            oscillator.type = 'sine';
            oscillator.frequency.value = 880;
            gainNode.gain.value = 0.5;
            
            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
            }, 200);
        }
        
        // Fonction de confirmation avant suppression de compte
        function confirmDeleteAccount() {
            if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
                let form = document.createElement("form");
                form.method = "post";
                form.action = "";
                
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "supp";
                input.value = "supp";
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Arrêter la caméra si la page est fermée
        window.addEventListener('beforeunload', stopScanner);
    </script>

    <?php
    // Gestion de la suppression de compte
    if (isset($_POST['supp'])) {
        // Supprimer d'abord les inscriptions et les présences
        try {
            $pdo->beginTransaction();
            
            // Supprimer les présences
            $sqlPresence = "DELETE FROM presences WHERE apogee = :apogee";
            $stmtPresence = $pdo->prepare($sqlPresence);
            $stmtPresence->execute(['apogee' => $apogee]);
            
            // Supprimer les inscriptions
            $sqlInscriptions = "DELETE FROM inscriptions WHERE id_etudiant = :apogee";
            $stmtInscriptions = $pdo->prepare($sqlInscriptions);
            $stmtInscriptions->execute(['apogee' => $apogee]);
            
            // Enfin, supprimer l'étudiant
            $sqlSupp = "DELETE FROM etudiants WHERE apogee = :apogee";
            $stmt = $pdo->prepare($sqlSupp);
            $stmt->execute(['apogee' => $apogee]);
            
            $pdo->commit();
            
            // Détruire la session et rediriger
            session_destroy();
            setcookie("apogee", "", time() - 3600, "/");
            setcookie("mot_de_passe", "", time() - 3600, "/");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<script>alert('Erreur lors de la suppression du compte: " . $e->getMessage() . "');</script>";
        }
    }
    ?>
</body>
</html>