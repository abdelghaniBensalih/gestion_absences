<?php 
session_start();
// Destruction des cookies selon le type d'utilisateur
if(isset($_COOKIE['apogee'])) {
    setcookie("apogee", "", time() - 3600, "/");
    setcookie("mot_de_passe", "", time() - 3600, "/");
    $userType = "étudiant";
}
if(isset($_COOKIE['id_administrateur'])) {
    setcookie("id_administrateur", "", time() - 3600, "/");
    setcookie("mot_de_passeAd", "", time() - 3600, "/");
    $userType = "administrateur";
}

session_destroy();

// Activer la redirection automatique après affichage du message
$redirect = true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - QR-Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .logout-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logout-icon {
            font-size: 4rem;
            color: #4e8cff;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite ease-in-out;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        h1 {
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background-color: #4e8cff;
            border-color: #4e8cff;
        }

        .progress-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            height: 5px;
            border-radius: 3px;
            margin-top: 20px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #4e8cff;
            width: 0%;
            transition: width 3s linear;
        }

        .countdown {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <i class="fas fa-sign-out-alt logout-icon"></i>
        <h1>Déconnexion réussie</h1>
        <p>Vous avez été déconnecté avec succès de votre compte <?= isset($userType) ? $userType : 'utilisateur' ?>.</p>
        
        <div class="buttons">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Se reconnecter
            </a>
            <a href="acceuil.php" class="btn btn-outline-light">
                <i class="fas fa-home me-2"></i>Page d'accueil
            </a>
        </div>

        <div class="progress-container">
            <div class="progress-bar" id="progress"></div>
        </div>
        <div class="countdown" id="countdown">Redirection dans <span id="seconds">3</span> secondes...</div>
    </div>

    <script>
        // Animation de la barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('progress');
            const countdownEl = document.getElementById('seconds');
            let seconds = 3;
            
            // Démarrer la barre de progression
            setTimeout(() => {
                progressBar.style.width = '100%';
            }, 100);
            
            // Compte à rebours
            const interval = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    <?php if($redirect): ?>
                    window.location.href = 'index.php';
                    <?php endif; ?>
                }
            }, 1000);
        });
    </script>
</body>
</html>