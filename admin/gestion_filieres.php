<?php
$title = "Gestion des filières";
require_once "../includes/header.php";
require "../config/db.php";

// Variables pour les messages
$successMessage = "";
$errorMessage = "";

// Traitement des formulaires
if(isset($_POST["nf"])) { 
    try {
        $sqlf = "INSERT INTO filieres(nom) VALUES (?);";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["nf"]]);
        $successMessage = "La filière a été ajoutée avec succès.";
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de l'ajout de la filière : " . $e->getMessage();
    }
}

if(isset($_POST["nfs"])) { 
    try {
        $sqlf = "DELETE FROM filieres WHERE nom = ?;";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["nfs"]]);
        if ($stmt->rowCount() > 0) {
            $successMessage = "La filière a été supprimée avec succès.";
        } else {
            $errorMessage = "Aucune filière avec ce nom n'a été trouvée.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la suppression : il existe probablement des modules ou étudiants liés à cette filière.";
    }
}

if(isset($_POST["anf"])) {
    try {
        $sqlf = "UPDATE filieres SET nom = ? WHERE nom = ?;";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$_POST["nnf"], $_POST["anf"]]);
        if ($stmt->rowCount() > 0) {
            $successMessage = "La filière a été modifiée avec succès.";
        } else {
            $errorMessage = "Aucune filière avec ce nom n'a été trouvée.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la modification de la filière : " . $e->getMessage();
    }
}

// Récupération des données pour l'affichage
$sqlf = "SELECT 
    f.id_filiere,
    f.nom AS nom_filiere,
    m.nom AS nom_module
FROM 
    filieres f
LEFT JOIN 
    modules m ON f.id_filiere = m.id_filiere
ORDER BY 
    f.nom, m.nom;";
$filieresData = $pdo->query($sqlf)->fetchAll();

// Récupérer la liste unique des filières pour les sélecteurs
$sqlFilieresUniques = "SELECT id_filiere, nom FROM filieres ORDER BY nom";
$filieresUniques = $pdo->query($sqlFilieresUniques)->fetchAll();

// Déterminer l'action en cours
$currentAction = 'dashboard';
if(isset($_POST["vlf"])) $currentAction = 'list';
if(isset($_POST["af"])) $currentAction = 'add';
if(isset($_POST["sf"])) $currentAction = 'delete';
if(isset($_POST["mf"])) $currentAction = 'edit';
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
        
        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-icon i {
            margin-right: 0.5rem;
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
                            <h2 class="card-title"><i class="fas fa-university me-2"></i>Gestion des filières</h2>
                        </div>
                        <div class="text-center">
                            <form action="" method="POST">
                                <button type="submit" name="vlf" class="btn btn-custom btn-list mb-3">
                                    <i class="fas fa-list-ul me-2"></i>Liste des filières et modules
                                </button><br>
                                
                                <button type="submit" name="af" class="btn btn-custom btn-add mb-3">
                                    <i class="fas fa-plus-circle me-2"></i>Ajouter une filière
                                </button><br>
                                
                                <button type="submit" name="sf" class="btn btn-custom btn-delete mb-3">
                                    <i class="fas fa-trash-alt me-2"></i>Supprimer une filière
                                </button><br>
                                
                                <button type="submit" name="mf" class="btn btn-custom btn-update mb-3">
                                    <i class="fas fa-edit me-2"></i>Modifier une filière
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php elseif ($currentAction === 'list'): ?>
            <!-- Liste des filières et modules -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-list-ul me-2"></i>Liste des filières et modules</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Filière</th>
                                        <th>Module</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $currentFiliere = '';
                                    foreach ($filieresData as $row): 
                                        $newFiliere = ($currentFiliere !== $row["nom_filiere"]);
                                        $currentFiliere = $row["nom_filiere"];
                                    ?>
                                    <tr>
                                        <td<?= $newFiliere ? ' class="fw-bold"' : '' ?>>
                                            <?= $newFiliere ? htmlspecialchars($row["nom_filiere"]) : '' ?>
                                        </td>
                                        <td><?= htmlspecialchars($row["nom_module"] ?? 'Aucun module') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($filieresData)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Aucune filière trouvée</td>
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
            <!-- Formulaire d'ajout de filière -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-plus-circle me-2"></i>Ajouter une filière</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="nf" class="form-label">Nom de la filière</label>
                                <input type="text" class="form-control" id="nf" name="nf" placeholder="Saisissez le nom de la filière" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom btn-add">
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
            <!-- Formulaire de suppression de filière -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-trash-alt me-2"></i>Supprimer une filière</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="nfs" class="form-label">Filière à supprimer</label>
                                <select class="form-select" name="nfs" id="nfs" required>
                                    <option value="" selected disabled>Sélectionnez une filière</option>
                                    <?php foreach ($filieresUniques as $filiere): ?>
                                        <option value="<?= htmlspecialchars($filiere['nom']) ?>">
                                            <?= htmlspecialchars($filiere['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ? Cette action est irréversible.')">
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
            <!-- Formulaire de modification de filière -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-edit me-2"></i>Modifier une filière</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="anf" class="form-label">Filière à modifier</label>
                                <select class="form-select" name="anf" id="anf" required>
                                    <option value="" selected disabled>Sélectionnez une filière</option>
                                    <?php foreach ($filieresUniques as $filiere): ?>
                                        <option value="<?= htmlspecialchars($filiere['nom']) ?>">
                                            <?= htmlspecialchars($filiere['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nnf" class="form-label">Nouveau nom</label>
                                <input type="text" class="form-control" id="nnf" name="nnf" placeholder="Saisissez le nouveau nom" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom btn-update">
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
    </script>
</body>
</html>