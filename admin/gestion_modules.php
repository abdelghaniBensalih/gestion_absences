<?php
$title = "Gestion des modules";
require_once "../includes/header.php";
require "../config/db.php";

// Variables pour les messages
$successMessage = "";
$errorMessage = "";

// Traitement d'ajout de module
if(isset($_POST["nm"])) { 
    try {
        $sqlf = "INSERT INTO modules(nom, nom_responsable, id_filiere, semestre) VALUES (?, ?, ?, ?);";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["n_m"], $_POST["nr"], $_POST["if"], $_POST["sem"]]);
        $successMessage = "Le module a été ajouté avec succès.";
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de l'ajout du module : " . $e->getMessage();
    }
}

// Traitement de suppression de module
if(isset($_POST["nms"])) { 
    try {
        $sqlf = "DELETE FROM modules WHERE id_module = ?;";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["nms"]]);
        if ($stmt->rowCount() > 0) {
            $successMessage = "Le module a été supprimé avec succès.";
        } else {
            $errorMessage = "Aucun module avec cet ID n'a été trouvé.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la suppression du module : il existe probablement des séances liées à ce module.";
    }
}

// Traitement de modification de module
if(isset($_POST["bakhta"])) {
    try {
        $sqlf = "UPDATE modules SET nom = ?, nom_responsable = ? WHERE nom = ? AND nom_responsable = ?;";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["nnm"], $_POST["nnr"], $_POST["anm"], $_POST["anr"]]);
        if ($stmt->rowCount() > 0) {
            $successMessage = "Le module a été modifié avec succès.";
        } else {
            $errorMessage = "Aucun module correspondant n'a été trouvé.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la modification du module : " . $e->getMessage();
    }
}

// Récupération des données pour l'affichage
$sqlModules = "SELECT m.id_module, m.nom, m.nom_responsable, m.id_filiere, m.semestre, f.nom AS nom_filiere 
               FROM modules m 
               LEFT JOIN filieres f ON m.id_filiere = f.id_filiere
               ORDER BY m.id_module;";
$modules = $pdo->query($sqlModules)->fetchAll();

$sqlFilieres = "SELECT id_filiere, nom FROM filieres ORDER BY nom;";
$filieres = $pdo->query($sqlFilieres)->fetchAll();

// Déterminer l'action en cours
$currentAction = 'dashboard';
if(isset($_POST["vlm"])) $currentAction = 'list';
if(isset($_POST["am"])) $currentAction = 'add';
if(isset($_POST["sm"])) $currentAction = 'delete';
if(isset($_POST["mm"])) $currentAction = 'edit';
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
        
        .btn-custom {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 1rem;
            min-width: 220px;
        }
        
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-list { 
            background-color: #4e8cff; 
            color: white; 
        }
        
        .btn-add { 
            background-color: #10b981; 
            color: white; 
        }
        
        .btn-delete { 
            background-color: #ef4444; 
            color: white; 
        }
        
        .btn-update { 
            background-color: #f59e0b; 
            color: white; 
        }
        
        .btn-back {
            background-color: #6b7280;
            color: white;
            margin-top: 1rem;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.25);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.25);
            color: #fff;
        }
        
        .form-select option {
            background-color: #0f3460;
            color: #fff;
        }
        
        .table {
            color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            background-color: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            font-weight: 600;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: background-color 0.3s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
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
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }
        
        .badge-primary {
            background-color: rgba(78, 140, 255, 0.2);
            color: #4e8cff;
        }
    </style>
</head>
<body>
    <div class="container">
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
        
        <?php if ($currentAction === 'dashboard'): ?>
            <!-- Dashboard principal -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-book me-2"></i>Gestion des modules</h2>
                        </div>
                        <div class="text-center">
                            <form action="" method="POST">
                                <button type="submit" name="vlm" class="btn btn-custom btn-list mb-3">
                                    <i class="fas fa-list-ul me-2"></i>Liste des modules
                                </button><br>
                                
                                <button type="submit" name="am" class="btn btn-custom btn-add mb-3">
                                    <i class="fas fa-plus-circle me-2"></i>Ajouter un module
                                </button><br>
                                
                                <button type="submit" name="sm" class="btn btn-custom btn-delete mb-3">
                                    <i class="fas fa-trash-alt me-2"></i>Supprimer un module
                                </button><br>
                                
                                <button type="submit" name="mm" class="btn btn-custom btn-update mb-3">
                                    <i class="fas fa-edit me-2"></i>Modifier un module
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php elseif ($currentAction === 'list'): ?>
            <!-- Liste des modules -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-list-ul me-2"></i>Liste des modules</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom du module</th>
                                        <th>Responsable</th>
                                        <th>Filière</th>
                                        <th>Semestre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($module["id_module"]) ?></td>
                                        <td><?= htmlspecialchars($module["nom"]) ?></td>
                                        <td><?= htmlspecialchars($module["nom_responsable"]) ?></td>
                                        <td><?= htmlspecialchars($module["nom_filiere"] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?= strtoupper(htmlspecialchars($module["semestre"])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($modules)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun module trouvé</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <form action="" method="POST">
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'add'): ?>
            <!-- Formulaire d'ajout de module -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-plus-circle me-2"></i>Ajouter un module</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="n_m" class="form-label">Nom du module</label>
                                <input type="text" class="form-control" id="n_m" name="n_m" placeholder="Saisissez le nom du module" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nr" class="form-label">Nom du responsable</label>
                                <input type="text" class="form-control" id="nr" name="nr" placeholder="Saisissez le nom du responsable" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="if" class="form-label">Filière associée</label>
                                <select class="form-select" id="if" name="if" required>
                                    <option value="" selected disabled>Sélectionnez une filière</option>
                                    <?php foreach ($filieres as $filiere): ?>
                                        <option value="<?= $filiere['id_filiere'] ?>">
                                            <?= htmlspecialchars($filiere['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sem" class="form-label">Semestre</label>
                                <select class="form-select" id="sem" name="sem" required>
                                    <option value="" selected disabled>Sélectionnez un semestre</option>
                                    <option value="s1">S1</option>
                                    <option value="s2">S2</option>
                                    <option value="s3">S3</option>
                                    <option value="s4">S4</option>
                                    <option value="s5">S5</option>
                                    <option value="s6">S6</option>
                                </select>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="nm" class="btn btn-custom btn-add">
                                    <i class="fas fa-plus-circle me-2"></i>Ajouter
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'delete'): ?>
            <!-- Formulaire de suppression de module -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-trash-alt me-2"></i>Supprimer un module</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="nms" class="form-label">Module à supprimer</label>
                                <select class="form-select" name="nms" id="nms" required>
                                    <option value="" selected disabled>Sélectionnez un module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= $module['id_module'] ?>">
                                            <?= htmlspecialchars($module['nom']) ?> - 
                                            <?= htmlspecialchars($module['nom_filiere'] ?? 'N/A') ?> - 
                                            <?= strtoupper(htmlspecialchars($module['semestre'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce module ? Cette action est irréversible.')">
                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'edit'): ?>
            <!-- Formulaire de modification de module -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-edit me-2"></i>Modifier un module</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="anm" class="form-label">Module à modifier</label>
                                <select class="form-select" name="module_select" id="module_select" onchange="fillModuleDetails()">
                                    <option value="" selected disabled>Sélectionnez un module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= htmlspecialchars($module['nom']) ?>" 
                                                data-responsable="<?= htmlspecialchars($module['nom_responsable']) ?>">
                                            <?= htmlspecialchars($module['nom']) ?> - 
                                            <?= htmlspecialchars($module['nom_responsable']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="anm" class="form-label">Ancien nom du module</label>
                                <input type="text" class="form-control" id="anm" name="anm" placeholder="Ancien nom du module" readonly required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nnm" class="form-label">Nouveau nom du module</label>
                                <input type="text" class="form-control" id="nnm" name="nnm" placeholder="Saisissez le nouveau nom du module" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="anr" class="form-label">Ancien nom du responsable</label>
                                <input type="text" class="form-control" id="anr" name="anr" placeholder="Ancien nom du responsable" readonly required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nnr" class="form-label">Nouveau nom du responsable</label>
                                <input type="text" class="form-control" id="nnr" name="nnr" placeholder="Saisissez le nouveau nom du responsable" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="bakhta" class="btn btn-custom btn-update">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fade-out pour les messages d'alerte après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 1s ease';
                        alert.style.opacity = '0';
                        
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 1000);
                    });
                }, 5000);
            }
        });
        
        // Fonction pour remplir automatiquement les détails du module à modifier
        function fillModuleDetails() {
            const select = document.getElementById('module_select');
            const option = select.options[select.selectedIndex];
            
            if (option && option.value) {
                document.getElementById('anm').value = option.value;
                document.getElementById('nnm').value = option.value;
                document.getElementById('anr').value = option.dataset.responsable;
                document.getElementById('nnr').value = option.dataset.responsable;
            }
        }
    </script>
</body>
</html>