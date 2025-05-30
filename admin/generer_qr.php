<?php
// Inclusion des fichiers nécessaires
$title = "Générer QR Code";
require_once "../includes/header.php";
require "../config/db.php";

// Traitement du formulaire de génération de QR code
$qrImageUrl = "";
$moduleId = "";
$moduleNom = "";
$seanceId = "";
$errorMessage = "";
$successMessage = "";

// Récupérer la liste des modules
try {
    $stmt = $pdo->query("SELECT id_module, nom, code FROM modules ORDER BY nom");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des modules: " . $e->getMessage();
}

// Si le formulaire est soumis
if (isset($_POST['generate_qr'])) {
    $moduleId = $_POST['module_id'];
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $duree = $_POST['duree'];
    $salle = $_POST['salle'];
    $type = $_POST['type_seance'];
    
    try {
        // Récupérer le nom du module
        $stmtModule = $pdo->prepare("SELECT nom FROM modules WHERE id_module = ?");
        $stmtModule->execute([$moduleId]);
        $moduleNom = $stmtModule->fetchColumn();
        
        // Vérifier si une séance existe déjà pour ce module, cette date et cette heure
        $dateHeure = $date . ' ' . $heure;
        $stmtCheck = $pdo->prepare("SELECT id_seance FROM seances 
                                    WHERE id_module = ? AND date_seance = ?");
        $stmtCheck->execute([$moduleId, $dateHeure]);
        $existingSeance = $stmtCheck->fetch();
        
        if ($existingSeance) {
            // Si une séance existe, utiliser son ID
            $seanceId = $existingSeance['id_seance'];
            $successMessage = "Séance existante trouvée. QR code généré pour la séance existante.";
        } else {
            // Sinon, créer une nouvelle séance
            $stmtSeance = $pdo->prepare("INSERT INTO seances (id_module, date_seance, duree, salle, type_seance) 
                                        VALUES (?, ?, ?, ?, ?)");
            $stmtSeance->execute([$moduleId, $dateHeure, $duree, $salle, $type]);
            $seanceId = $pdo->lastInsertId();
            $successMessage = "Nouvelle séance créée et QR code généré avec succès.";
        }
        
        // Générer les données pour le QR code
        $qrData = json_encode([
            'seance_id' => $seanceId,
            'module_id' => $moduleId,
            'timestamp' => time()
        ]);
        
        // Encoder pour URL
        $qrDataEncoded = base64_encode($qrData);
        
        // Générer l'URL pour l'API de QR code
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrDataEncoded);
    } catch (PDOException $e) {
        $errorMessage = "Erreur: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            color: #fff;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .container {
            padding-top: 2rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-header {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem;
            margin: -2rem -2rem 1.5rem -2rem;
        }
        
        .card-title {
            margin: 0;
            font-weight: 600;
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
        }
        
        .qr-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .qr-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .info-value {
            font-weight: 600;
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.25);
            color: #fff;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4e8cff, #3b70d8);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3b70d8, #2a5bb9);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.2);
            border-left: 4px solid #10b981;
            color: #fff;
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.2);
            border-left: 4px solid #ef4444;
            color: #fff;
        }
        
        .download-btn {
            background-color: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin-top: 15px;
        }
        
        .download-btn:hover {
            background-color: #0d9668;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        select option {
    background-color: #1a1a2e; /* Fond foncé */
    color: #fff; /* Texte blanc */
}

/* Ajuster le style pour les options sélectionnées */
select option:checked, 
select option:hover {
    background-color: #3a70e3; /* Bleu pour la sélection */
    color: #fff;
}

/* Pour Firefox */
select {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.1);
}

/* Pour Chrome/Safari/Edge - forcer la couleur des options */
select.form-select {
    color-scheme: dark;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-qrcode me-2"></i>Générer un QR Code de présence</h2>
                    </div>
                    
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $errorMessage ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="module_id" class="form-label">Module</label>
                            <select class="form-select" id="module_id" name="module_id" required>
                                <option value="" disabled selected>Sélectionnez un module</option>
                                <?php foreach ($modules as $module): ?>
                                    <option value="<?= $module['id_module'] ?>" <?= ($moduleId == $module['id_module']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($module['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="heure" class="form-label">Heure</label>
                            <input type="time" class="form-control" id="heure" name="heure" value="<?= date('H:i') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duree" class="form-label">Durée (minutes)</label>
                            <input type="number" class="form-control" id="duree" name="duree" value="120" min="3" step="15" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="salle" class="form-label">Salle</label>
                            <input type="text" class="form-control" id="salle" name="salle" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type_seance" class="form-label">Type de séance</label>
                            <select class="form-select" id="type_seance" name="type_seance" required>
                                <option value="Cours">Cours Magistral (CM)</option>
                                <option value="TD">Travaux Dirigés (TD)</option>
                                <option value="TP">Travaux Pratiques (TP)</option>
                            </select>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" name="generate_qr" class="btn btn-primary">
                                <i class="fas fa-qrcode me-2"></i>Générer QR Code
                            </button>
                            <a href="gestion_absences.php" class="btn btn-back ms-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($qrImageUrl)): ?>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-check-circle me-2"></i>QR Code généré</h2>
                    </div>
                    
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?= $successMessage ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="qr-container">
                        <img src="<?= $qrImageUrl ?>" alt="QR Code" class="img-fluid">
                        <a href="<?= $qrImageUrl ?>" download="qrcode_seance_<?= $seanceId ?>.png" class="download-btn">
                            <i class="fas fa-download me-2"></i>Télécharger
                        </a>
                    </div>
                    
                    <div class="qr-info">
                        <h5 class="text-center mb-3">Informations de la séance</h5>
                        <div class="info-item">
                            <span class="info-label">Module:</span>
                            <span class="info-value"><?= htmlspecialchars($moduleNom) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date:</span>
                            <span class="info-value"><?= htmlspecialchars($_POST['date']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Heure:</span>
                            <span class="info-value"><?= htmlspecialchars($_POST['heure']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Durée:</span>
                            <span class="info-value"><?= htmlspecialchars($_POST['duree']) ?> minutes</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Salle:</span>
                            <span class="info-value"><?= htmlspecialchars($_POST['salle']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Type:</span>
                            <span class="info-value"><?= htmlspecialchars($_POST['type_seance']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">ID Séance:</span>
                            <span class="info-value"><?= $seanceId ?></span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>Affichez ce QR code sur le projecteur pour que les étudiants puissent scanner et enregistrer leur présence.
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Définir la date d'aujourd'hui comme valeur minimale pour le champ date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').min = today;
        });
    </script>
</body>
</html>