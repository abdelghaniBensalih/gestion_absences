<?php
$title = "Gestion des étudiants";
require_once "../includes/header.php";
require "../config/db.php";

// Variables pour les messages
$successMessage = "";
$errorMessage = "";

// Traitement de la suppression d'un étudiant
if(isset($_POST["nes"])) {
    $apogee = $_POST["nes"];
    try {
        $sqlf = "DELETE FROM etudiants WHERE apogee = ?";
        $stmt = $pdo->prepare($sqlf);
        $stmt->execute([$apogee]);
        
        if($stmt->rowCount() > 0) {
            $successMessage = "L'étudiant avec le numéro Apogée $apogee a été supprimé avec succès.";
        } else {
            $errorMessage = "Aucun étudiant avec le numéro Apogée $apogee n'a été trouvé.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la suppression: il existe probablement des inscriptions liées à cet étudiant.";
    }
}

// Traitement de la modification d'un étudiant
if(isset($_POST["update_student"])) {
    $apogee = $_POST["apogee"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    
    try {
        $sql = "UPDATE etudiants SET nom = ?, prenom = ?, email = ? WHERE apogee = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $apogee]);
        
        if($stmt->rowCount() > 0) {
            $successMessage = "Les informations de l'étudiant ont été mises à jour avec succès.";
        } else {
            $errorMessage = "Aucune modification n'a été effectuée.";
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de la mise à jour: " . $e->getMessage();
    }
}

// Récupération de tous les étudiants pour le formulaire de modification
$allStudents = [];
try {
    $sqlAllStudents = "SELECT apogee, nom, prenom, email FROM etudiants ORDER BY nom, prenom";
    $allStudents = $pdo->query($sqlAllStudents)->fetchAll();
} catch(PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des étudiants: " . $e->getMessage();
}

// Déterminer l'action en cours
$currentAction = 'dashboard';
if(isset($_POST["vle"])) $currentAction = 'list';
if(isset($_POST["se"])) $currentAction = 'delete';
if(isset($_POST["me"])) $currentAction = 'edit';
if(isset($_POST["get_student_info"])) $currentAction = 'edit_form';
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-custom i {
            margin-right: 8px;
        }
        
        .btn-list { 
            background-color: #4e8cff; 
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
            text-align: left;
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
                            <h2 class="card-title"><i class="fas fa-user-graduate me-2"></i>Gestion des étudiants</h2>
                        </div>
                        <div class="text-center">
                            <form action="" method="POST">
                                <button type="submit" name="vle" class="btn btn-custom btn-list">
                                    <i class="fas fa-list-ul"></i>Liste des étudiants
                                </button><br>
                                
                                <button type="submit" name="se" class="btn btn-custom btn-delete">
                                    <i class="fas fa-trash-alt"></i>Supprimer un étudiant
                                </button><br>
                                
                                <button type="submit" name="me" class="btn btn-custom btn-update">
                                    <i class="fas fa-edit"></i>Modifier un étudiant
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php elseif ($currentAction === 'list'): ?>
            <!-- Liste des étudiants -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-list-ul me-2"></i>Liste des étudiants</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Apogée</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Module</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    try {
                                        $sqle = "SELECT 
                                            m.nom AS nom_module,
                                            e.apogee,
                                            e.nom AS nom_etudiant,
                                            e.prenom,
                                            e.email
                                        FROM 
                                            inscriptions i
                                        JOIN 
                                            etudiants e ON i.id_etudiant = e.apogee
                                        JOIN 
                                            modules m ON i.id_module = m.id_module
                                        ORDER BY 
                                            m.nom, e.nom;";
                                        
                                        $lignee = $pdo->query($sqle)->fetchAll();
                                        
                                        if (count($lignee) > 0) {
                                            foreach($lignee as $e) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($e["apogee"]) . '</td>';
                                                echo '<td>' . htmlspecialchars($e["nom_etudiant"]) . '</td>';
                                                echo '<td>' . htmlspecialchars($e["prenom"]) . '</td>';
                                                echo '<td>' . htmlspecialchars($e["email"]) . '</td>';
                                                echo '<td>' . htmlspecialchars($e["nom_module"]) . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center">Aucun étudiant inscrit trouvé</td></tr>';
                                        }
                                    } catch(PDOException $e) {
                                        echo '<tr><td colspan="5" class="text-center text-danger">Erreur: ' . $e->getMessage() . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <form action="" method="POST">
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left"></i>Retour
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'delete'): ?>
            <!-- Formulaire de suppression d'étudiant -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-trash-alt me-2"></i>Supprimer un étudiant</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="nes" class="form-label">Étudiant à supprimer</label>
                                <select class="form-select" name="student_select" id="student_select" onchange="updateApogeeField()">
                                    <option value="" selected disabled>Sélectionnez un étudiant</option>
                                    <?php foreach ($allStudents as $student): ?>
                                        <option value="<?= $student['apogee'] ?>">
                                            <?= htmlspecialchars($student['nom']) ?> <?= htmlspecialchars($student['prenom']) ?> 
                                            (<?= htmlspecialchars($student['apogee']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="nes" name="nes" value="">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ? Cette action est irréversible.')">
                                    <i class="fas fa-trash-alt"></i>Supprimer
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'edit'): ?>
            <!-- Sélection d'étudiant à modifier -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-edit me-2"></i>Modifier un étudiant</h2>
                        </div>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Sélectionnez un étudiant</label>
                                <select class="form-select" name="student_id" id="student_id" required>
                                    <option value="" selected disabled>Choisir un étudiant</option>
                                    <?php foreach ($allStudents as $student): ?>
                                        <option value="<?= $student['apogee'] ?>">
                                            <?= htmlspecialchars($student['nom']) ?> <?= htmlspecialchars($student['prenom']) ?> 
                                            (<?= htmlspecialchars($student['apogee']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="get_student_info" class="btn btn-custom btn-update">
                                    <i class="fas fa-edit"></i>Continuer
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        <?php elseif ($currentAction === 'edit_form'): ?>
            <!-- Formulaire de modification d'étudiant -->
            <?php
            $studentId = $_POST['student_id'];
            $studentInfo = null;
            
            // Récupérer les informations de l'étudiant sélectionné
            try {
                $sql = "SELECT apogee, nom, prenom, email FROM etudiants WHERE apogee = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$studentId]);
                $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                $errorMessage = "Erreur lors de la récupération des informations: " . $e->getMessage();
            }
            
            if ($studentInfo):
            ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-edit me-2"></i>Modifier l'étudiant</h2>
                        </div>
                        <form action="" method="POST">
                            <input type="hidden" name="apogee" value="<?= htmlspecialchars($studentInfo['apogee']) ?>">
                            
                            <div class="mb-3">
                                <label for="apogee_display" class="form-label">Numéro Apogée</label>
                                <input type="text" class="form-control" id="apogee_display" value="<?= htmlspecialchars($studentInfo['apogee']) ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($studentInfo['nom']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($studentInfo['prenom']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($studentInfo['email']) ?>" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="update_student" class="btn btn-custom btn-update">
                                    <i class="fas fa-save"></i>Enregistrer les modifications
                                </button>
                                <button type="submit" class="btn btn-custom btn-back">
                                    <i class="fas fa-arrow-left"></i>Retour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Étudiant non trouvé.
                </div>
                <div class="text-center">
                    <form action="" method="POST">
                        <button type="submit" class="btn btn-custom btn-back">
                            <i class="fas fa-arrow-left"></i>Retour
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Fonction pour remplir automatiquement le champ caché avec la valeur de l'apogée
        function updateApogeeField() {
            const select = document.getElementById('student_select');
            const hiddenField = document.getElementById('nes');
            hiddenField.value = select.value;
        }
        
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