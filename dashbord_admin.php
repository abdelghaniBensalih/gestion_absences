<?php
// Commencer la session avant toute autre opération
session_start();
include_once 'config/db.php'; // Inclure le fichier de connexion à la base de données
// Fonction simplifiée pour vérifier l'authentification admin sans dépendances
function isAdminAuthenticated() {
    return isset($_SESSION['authAdmin']) && $_SESSION['authAdmin'] === "Oui";
}

// Vérification directe sans dépendances
if (!isAdminAuthenticated()) {
    header("Location: index.php");
    exit();
}

// Statistiques - Récupération directe
try {
    // Nombre d'étudiants
    $stmtEtudiants = $pdo->query("SELECT COUNT(*) as total FROM etudiants");
    $totalEtudiants = $stmtEtudiants->fetch()['total'];
    
    // Nombre de modules
    $stmtModules = $pdo->query("SELECT COUNT(*) as total FROM modules");
    $totalModules = $stmtModules->fetch()['total'];
    
    // Nombre de filières
    $stmtFilieres = $pdo->query("SELECT COUNT(*) as total FROM filieres");
    $totalFilieres = $stmtFilieres->fetch()['total'];
    
    // Nombre total d'absences
    $stmtAbsences = $pdo->query("SELECT COUNT(*) as total FROM absences");
    $totalAbsences = $stmtAbsences->fetch()['total'];
} catch (PDOException $e) {
    $totalEtudiants = "N/A";
    $totalModules = "N/A";
    $totalFilieres = "N/A";
    $totalAbsences = "N/A";
}

$title = "Tableau de bord administrateur";
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
        }
        
        .dashboard-container {
            padding: 2rem 0;
        }
        
        .greeting-section {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .greeting-section h1 {
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 2.2rem;
        }
        
        .greeting-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }
        
        .stats-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            flex: 1;
            min-width: 220px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: inline-block;
            padding: 15px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .stat-icon.students { color: #4e8cff; }
        .stat-icon.modules { color: #10b981; }
        .stat-icon.branches { color: #f59e0b; }
        .stat-icon.absences { color: #ef4444; }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .actions-section {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-out;
            animation-delay: 0.5s;
            animation-fill-mode: both;
        }
        
        .actions-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
            color: white;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }
        
        .action-button {
            display: flex;
            align-items: center;
            padding: 1.2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
            font-weight: 500;
            font-size: 1rem;
            text-align: left;
            cursor: pointer;
        }
        
        .action-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            z-index: -1;
            transition: all 0.3s ease;
        }
        
        .action-button:hover::before {
            opacity: 0.7;
        }
        
        .action-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .action-button i {
            font-size: 1.5rem;
            margin-right: 15px;
            transition: all 0.3s ease;
        }
        
        .action-button:hover i {
            transform: scale(1.2);
        }
        
        .btn-filiere {
            background-color: #3b82f6;
        }
        
        .btn-modules {
            background-color: #10b981;
        }
        
        .btn-etudiants {
            background-color: #f59e0b;
        }
        
        .btn-absences {
            background-color: #ef4444;
        }
        
        .btn-logout {
            background-color: #6b7280;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2rem auto 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-logout:hover {
            background-color: #4b5563;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-logout i {
            margin-right: 8px;
        }
        
        /* Menu de navigation simplifié */
        .navbar {
            background: rgba(15, 52, 96, 0.95);
            padding: 1rem 0;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            gap: 1rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: white;
        }
        
        /* Footer simplifié */
        .footer {
            background: rgba(15, 52, 96, 0.95);
            color: #fff;
            padding: 1.5rem 0;
            margin-top: 3rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
            }
            
            .stat-card {
                width: 100%;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .greeting-section h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation simplifiée -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">QR-Présence</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Accueil</a>
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <div class="greeting-section">
            <h1><i class="fas fa-user-shield me-3"></i>Tableau de bord administrateur</h1>
            <p>Bienvenue dans votre espace de gestion QR-Présence. Gérez les filières, modules, étudiants et absences facilement.</p>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon students">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-value"><?= $totalEtudiants ?></div>
                <div class="stat-label">Étudiants</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon modules">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-value"><?= $totalModules ?></div>
                <div class="stat-label">Modules</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon branches">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-value"><?= $totalFilieres ?></div>
                <div class="stat-label">Filières</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon absences">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-value"><?= $totalAbsences ?></div>
                <div class="stat-label">Absences</div>
            </div>
        </div>
        
        <div class="actions-section">
            <h2>Gestion de la plateforme</h2>
            
            <div class="action-buttons">
                <a href="admin/gestion_filieres.php" class="action-button btn-filiere">
                    <i class="fas fa-university"></i>
                    <span>Gestion des filières</span>
                </a>
                
                <a href="admin/gestion_modules.php" class="action-button btn-modules">
                    <i class="fas fa-book"></i>
                    <span>Gestion des modules</span>
                </a>
                
                <a href="admin/gestion_etudiants.php" class="action-button btn-etudiants">
                    <i class="fas fa-user-graduate"></i>
                    <span>Gestion des étudiants</span>
                </a>
                
                <a href="admin/gestion_absences.php" class="action-button btn-absences">
                    <i class="fas fa-calendar-times"></i>
                    <span>Gestion des absences</span>
                </a>
            </div>
            
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <!-- Footer simplifié -->
    <div class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> QR-Présence. Tous droits réservés.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation des statistiques avec comptage
        document.addEventListener("DOMContentLoaded", function() {
            const statValues = document.querySelectorAll('.stat-value');
            
            statValues.forEach(statValue => {
                const finalValue = parseInt(statValue.textContent);
                if (!isNaN(finalValue)) {
                    let startValue = 0;
                    const duration = 1500;
                    const increment = Math.ceil(finalValue / (duration / 20));
                    
                    const counter = setInterval(function() {
                        startValue += increment;
                        
                        if (startValue >= finalValue) {
                            statValue.textContent = finalValue;
                            clearInterval(counter);
                        } else {
                            statValue.textContent = startValue;
                        }
                    }, 20);
                }
            });
        });
    </script>
</body>
</html>