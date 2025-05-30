<?php
$title = "Gestion des absences";
require_once "../includes/header.php";
require "../config/db.php";

// Variables pour les messages
$successMessage = "";
$errorMessage = "";

// Récupérer les filtres
$moduleId = isset($_GET['module']) ? intval($_GET['module']) : null;
$etudiantId = isset($_GET['etudiant']) ? intval($_GET['etudiant']) : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;
$justifiee = isset($_GET['justifiee']) ? $_GET['justifiee'] : null;

// Traitement de la justification d'absence
if(isset($_POST['justifier_absence'])) {
    $absenceId = isset($_POST['absence_id']) ? intval($_POST['absence_id']) : 0;
    $motif = isset($_POST['motif']) ? trim($_POST['motif']) : '';
    $document = ""; // À implémenter: téléchargement de document
    
    try {
        // Récupérer l'ID administrateur depuis la session
        session_start(); // Si pas déjà fait
        $adminId = isset($_SESSION['id_administrateur']) ? $_SESSION['id_administrateur'] : 1; // Utiliser 1 par défaut pour le test
        
        $stmt = $pdo->prepare("UPDATE absences SET justifiee = 1, motif = ?, date_justification = NOW(), validee_par = ? WHERE id_absence = ?");
        $stmt->execute([$motif, $adminId, $absenceId]); // Exécuter avec les paramètres
        
        if($stmt->rowCount() > 0) {
            $successMessage = "L'absence a été justifiée avec succès.";
        } else {
            $errorMessage = "Erreur: l'absence n'a pas pu être justifiée ou déjà justifiée.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la justification: " . $e->getMessage();
    }
}

// Récupérer les données pour les filtres
try {
    $stmtModules = $pdo->query("SELECT id_module, nom, code FROM modules ORDER BY nom");
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Nombre de modules trouvés: " . count($modules) . " -->";
    $stmtEtudiants = $pdo->query("SELECT apogee, nom, prenom FROM etudiants ORDER BY nom, prenom");
    $etudiants = $stmtEtudiants->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des données: " . $e->getMessage();
}

// Construire la requête pour les absences avec filtres
$sql = "SELECT a.id_absence, a.justifiee, a.motif, a.date_justification,
               e.apogee, e.nom AS etudiant_nom, e.prenom AS etudiant_prenom, 
               m.id_module, m.nom AS module_nom, m.code AS module_code,
               s.date_seance, s.salle, s.type_seance, s.duree,
               ad.nom AS admin_nom, ad.prenom AS admin_prenom
        FROM absences a
        JOIN etudiants e ON a.apogee = e.apogee
        JOIN seances s ON a.id_seance = s.id_seance
        JOIN modules m ON s.id_module = m.id_module
        LEFT JOIN administrateurs ad ON a.validee_par = ad.id_administrateur
        WHERE 1=1";

$params = [];

if ($moduleId) {
    $sql .= " AND m.id_module = ?";
    $params[] = $moduleId;
}

if ($etudiantId) {
    $sql .= " AND e.apogee = ?";
    $params[] = $etudiantId;
}

if ($date) {
    $sql .= " AND DATE(s.date_seance) = ?";
    $params[] = $date;
}

if ($justifiee !== null) {
    $sql .= " AND a.justifiee = ?";
    $params[] = $justifiee;
}

$sql .= " ORDER BY s.date_seance DESC, e.nom, e.prenom";

// Récupérer les absences
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $absences = $stmt->fetchAll();
} catch(PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des absences: " . $e->getMessage();
    $absences = [];
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
            margin-bottom: 2rem;
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
        }
        
        .btn-custom {
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4e8cff, #3a70e3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
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
        }
        
        .table {
            color: #fff;
        }
        
        .table thead th {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }
        
        .table tbody tr {
            border-color: rgba(255, 255, 255, 0.05);
        }
        
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 6px;
        }
        
        .badge-success {
            background-color: #10b981;
        }
        
        .badge-danger {
            background-color: #ef4444;
        }
        
        .toolbar-btns {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .modal-content {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #fff;
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-title {
            font-weight: 600;
            color: #fff;
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
        
        .dataTable tbody tr td {
            vertical-align: middle;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
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
/* Style spécifique pour la liste déroulante des étudiants */
#etudiant option {
    background-color: #1a1a2e !important; 
    color: #ffffff !important;
}

/* Rendre plus visible le texte dans toutes les listes déroulantes */
select option {
    background-color: #1a1a2e !important; 
    color: #ffffff !important;
    text-shadow: 0 0 0 #ffffff; /* Ajoute un effet de netteté au texte */
}

/* Assurer que la couleur du texte reste visible même avec les règles du navigateur */
@-moz-document url-prefix() {
    select {
        color: #ffffff;
    }
    select option {
        color: #ffffff !important;
    }
}

/* Pour Chrome et Safari - forcer le contraste */
@media screen and (-webkit-min-device-pixel-ratio:0) {
    select {
        background-color: rgba(26, 26, 46, 0.9) !important;
    }
    select option {
        background-color: #1a1a2e !important;
        color: #ffffff !important;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4"><i class="fas fa-calendar-times me-2"></i>Gestion des absences</h1>
        
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $successMessage ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $errorMessage ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtres</h5>
            </div>
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="module" class="form-label">Module</label>
                        <select name="module" id="module" class="form-select">
                            <option value="">Tous les modules</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['id_module'] ?>" <?= $moduleId == $module['id_module'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($module['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="etudiant" class="form-label">Étudiant</label>
                        <select name="etudiant" id="etudiant" class="form-select">
                            <option value="">Tous les étudiants</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?= $etudiant['apogee'] ?>" <?= $etudiantId == $etudiant['apogee'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?> (<?= $etudiant['apogee'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="<?= $date ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="justifiee" class="form-label">Justification</label>
                        <select name="justifiee" id="justifiee" class="form-select">
                            <option value="">Toutes</option>
                            <option value="1" <?= $justifiee === "1" ? 'selected' : '' ?>>Justifiées</option>
                            <option value="0" <?= $justifiee === "0" ? 'selected' : '' ?>>Non justifiées</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                        <a href="gestion_absences.php" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="toolbar-btns">
            <a href="generer_qr.php" class="btn btn-primary">
                <i class="fas fa-qrcode me-2"></i>Générer QR Code
            </a>
            <a href="../dashbord_admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title"><i class="fas fa-list me-2"></i>Liste des absences</h5>
            </div>
            
            <?php if (empty($absences)): ?>
                <div class="text-center p-4">
                    <i class="fas fa-info-circle fa-3x mb-3" style="opacity: 0.5;"></i>
                    <p>Aucune absence trouvée avec les critères sélectionnés.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="absencesTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Étudiant</th>
                                <th>Module</th>
                                <th>Type</th>
                                <th>Salle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $absence): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($absence['date_seance'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($absence['etudiant_nom'] . ' ' . $absence['etudiant_prenom']) ?>
                                        <div><small><?= $absence['apogee'] ?></small></div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($absence['module_nom']) ?>
                                        <div><small><?= $absence['module_code'] ?></small></div>
                                    </td>
                                    <td><?= $absence['type_seance'] ?></td>
                                    <td><?= htmlspecialchars($absence['salle']) ?></td>
                                    <td>
                                        <?php if ($absence['justifiee']): ?>
                                            <span class="badge badge-success">Justifiée</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Non justifiée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$absence['justifiee']): ?>
                                            <button class="btn btn-sm btn-success" onclick="justifierAbsence(<?= $absence['id_absence'] ?>, '<?= htmlspecialchars($absence['etudiant_nom'] . ' ' . $absence['etudiant_prenom']) ?>')">
                                                Justifier
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-info" onclick="voirJustification(<?= $absence['id_absence'] ?>, '<?= htmlspecialchars($absence['motif']) ?>', '<?= date('d/m/Y', strtotime($absence['date_justification'])) ?>', '<?= htmlspecialchars($absence['admin_nom'] . ' ' . $absence['admin_prenom']) ?>')">
                                                Détails
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal de justification d'absence -->
    <div class="modal fade" id="justifierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Justifier une absence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="absence_id" id="absence_id">
                        <div class="mb-3">
                            <label for="etudiant_name" class="form-label">Étudiant</label>
                            <input type="text" class="form-control" id="etudiant_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="motif" class="form-label">Motif de justification</label>
                            <textarea class="form-control" name="motif" id="motif" rows="3" required></textarea>
                        </div>
                        <!-- Pour une future implémentation: téléchargement de document
                        <div class="mb-3">
                            <label for="document" class="form-label">Document justificatif</label>
                            <input type="file" class="form-control" name="document" id="document">
                        </div>
                        -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="justifier_absence" class="btn btn-success">Valider la justification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal de détails de justification -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la justification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <p id="detail_motif" class="p-2 bg-dark rounded"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Justifiée le</label>
                        <p id="detail_date" class="p-2 bg-dark rounded"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validée par</label>
                        <p id="detail_admin" class="p-2 bg-dark rounded"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Initialisation de DataTable
        $(document).ready(function() {
            $('#absencesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                pageLength: 15,
                responsive: true
            });
            
            // Fade-out des messages d'alerte après 5 secondes
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
        
        // Fonction pour ouvrir le modal de justification
        function justifierAbsence(id, nom) {
            document.getElementById('absence_id').value = id;
            document.getElementById('etudiant_name').value = nom;
            
            const modal = new bootstrap.Modal(document.getElementById('justifierModal'));
            modal.show();
        }
        
        // Fonction pour voir les détails d'une justification
        function voirJustification(id, motif, date, admin) {
            document.getElementById('detail_motif').textContent = motif || 'Non spécifié';
            document.getElementById('detail_date').textContent = date;
            document.getElementById('detail_admin').textContent = admin || 'Non spécifié';
            
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();
        }
    </script>
</body>
</html>