<?php
session_start();
// destruction des cookies quand l'user est deconnecte
if(isset($_POST['deconnecte'])){
    setcookie("apogee", "", time() - 3600, "/");
    setcookie("mot_de_passe", "", time() - 3600, "/");
    session_destroy();
    header("Location: index.php");
    exit();
}
if(isset($_POST['deconnecteAd'])){
    setcookie("id_administrateur", "", time() - 3600, "/");
    setcookie("mot_de_passeAd", "", time() - 3600, "/");
    session_destroy();
    header("Location: index.php");
    exit();
}
//pour pre-remplir les champs de connexion
$apogee = $_COOKIE['apogee'] ?? null;
$mot_de_passe = $_COOKIE['mot_de_passe'] ?? null;
$mot_de_passeAd = $_COOKIE['mot_de_passeAd'] ?? null;
$id_administrateur = $_COOKIE['id_administrateur'] ?? null;
// redirection si l'utilisateur est deja connecté
// if(isset($_COOKIE['apogee']) && isset($_COOKIE['mot_de_passe'])) {
//     header("Location: dashbord_etudiant.php");
//     exit();
// }
// if(isset($_COOKIE['id_administrateur']) && isset($_COOKIE['mot_de_passeAd'])) {
//     header("Location: dashbord_Admin.php");
//     exit();
// }

$title = "Connexion";
require "config/db.php";

header('Content-Type: text/html; charset=UTF-8');

$error = "";
$errorAd = "";
$activeTab = isset($_POST['admin']) || isset($_POST['subAdm']) ? 'admin' : 'etudiant';

if (isset($_POST['subEtu'])) {
    $nom = $_POST['apogee'];
    $password = $_POST['password'];
    $_SESSION['apogee'] = $nom;
    $tmp = false;
    foreach ($lignes as $ligne) {
        if ($ligne['apogee'] == $nom && password_verify($password, $ligne['mot_de_passe'])) {
            $tmp = true;
        }
    }
    if ($tmp) {
        setcookie("apogee", $nom, time() + 3600 * 24, "/");
        setcookie("mot_de_passe", $password, time() + 3600 * 24, "/");
        $_SESSION['auth'] = "Oui";
        $error = "";
        header("location:dashbord_etudiant.php");
        exit();
    } else {
        $error = <<<_HTML
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> Le mot de passe et/ou Apogée est incorrect.
            </div>
        _HTML;
        $activeTab = 'etudiant';
    }
}

if (isset($_POST['subAdm'])) {
    $nom = $_POST['id'];
    $password = $_POST['password'];
    $Tmp = false;
    foreach ($lignesAd as $ligne) {
        if ($ligne['id_administrateur'] == $nom && $ligne['mot_de_passe'] ==$password) {
            $Tmp = true;
        }
    }
    if ($Tmp) {
        setcookie("id_administrateur", $nom, time() + 3600 * 24, "/");
        setcookie("mot_de_passeAd", $password, time() + 3600 * 24, "/");
        $_SESSION['authAdmin'] = "Oui";
        $errorAd = "";
        header("location:dashbord_admin.php");
        exit();
    } else {
        $errorAd = <<<_HTML
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> Le mot de passe et/ou identifiant est incorrect.
            </div>
        _HTML;
        $activeTab = 'admin';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - QR-Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f3460, #1a1a2e);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #0f3460;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }

        .loader {
            max-width: 15rem;
            width: 100%;
            height: auto;
            stroke-linecap: round;
        }

        circle {
            fill: none;
            stroke-width: 3.5;
            animation-name: preloader;
            animation-duration: 3s;
            animation-iteration-count: infinite;
            animation-timing-function: ease-in-out;
            transform-origin: 170px 170px;
            will-change: transform;
        }

        circle:nth-of-type(1) {
            stroke-dasharray: 550px;
            stroke: #4e8cff;
            animation-delay: -0.15s;
        }

        circle:nth-of-type(2) {
            stroke-dasharray: 500px;
            stroke: #ffffff;
            animation-delay: -0.30s;
        }

        circle:nth-of-type(3) {
            stroke-dasharray: 450px;
            stroke: #4e8cff;
            animation-delay: -0.45s;
        }

        circle:nth-of-type(4) {
            stroke-dasharray: 300px;
            stroke: #ffffff;
            animation-delay: -0.60s;
        }

        @keyframes preloader {
            50% {
                transform: rotate(360deg);
            }
        }

        .main-content {
            display: none;
            flex-grow: 1;
            padding-top: 50px;
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .back-home i {
            margin-right: 5px;
        }

        .back-home:hover {
            color: #4e8cff;
            transform: translateX(-3px);
        }

        .tabs-container {
            max-width: 500px;
            margin: 0 auto 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .nav-pills {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 5px;
            border-radius: 10px;
        }

        .nav-pills .nav-link {
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #4e8cff;
            color: white;
            box-shadow: 0 4px 10px rgba(78, 140, 255, 0.5);
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(78, 140, 255, 0.1);
            z-index: -1;
        }

        .login-card::after {
            content: '';
            position: absolute;
            bottom: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(78, 140, 255, 0.1);
            z-index: -1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }

        .login-card h2::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background-color: #4e8cff;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .form-field {
            position: relative;
            margin-bottom: 20px;
        }

        .form-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-field input {
            width: 100%;
            padding: 13px 15px;
            padding-left: 45px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-field input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-field input:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(78, 140, 255, 0.25);
        }

        .form-field .icon {
            position: absolute;
            left: 15px;
            top: 36px;
            color: rgba(255, 255, 255, 0.7);
        }

        .field-icon {
            position: absolute;
            right: 15px;
            top: 39px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
        }

        .submit-btn {
            width: 100%;
            background-color: #4e8cff;
            color: #fff;
            font-weight: 600;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background-color: #3a78e7;
            box-shadow: 0 5px 15px rgba(78, 140, 255, 0.4);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .message a {
            color: #4e8cff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .message a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid #dc3545;
            color: #fff;
            padding: 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            margin: 15px 0 0;
            display: flex;
            align-items: center;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
            }
        }

        .theme-btn-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .theme-btn {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .theme-btn:hover {
            transform: scale(1.2);
        }
    </style>
</head>

<body>
    <div class="loader-container" id="loader-container">
        <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 340">
            <circle cx="170" cy="170" r="160" />
            <circle cx="170" cy="170" r="135" />
            <circle cx="170" cy="170" r="110" />
            <circle cx="170" cy="170" r="85" />
        </svg>
    </div>

    <div class="main-content" id="main-content">
        <a href="acceuil.php" class="back-home">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>

        <div class="tabs-container">
            <form action="" method="post">
                <ul class="nav nav-pills nav-fill" id="pillNav" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'etudiant' ? 'active' : '' ?>" name="etudiant" type="submit" role="tab" aria-selected="<?= $activeTab === 'etudiant' ? 'true' : 'false' ?>">
                            <i class="fas fa-user-graduate me-2"></i>Étudiant(e)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'admin' ? 'active' : '' ?>" name="admin" type="submit" role="tab" aria-selected="<?= $activeTab === 'admin' ? 'true' : 'false' ?>">
                            <i class="fas fa-user-shield me-2"></i>Administrateur
                        </button>
                    </li>
                </ul>
            </form>
        </div>

        <div class="login-container">
            <div class="login-card">
                <?php if ($activeTab === 'etudiant'): ?>
                    <h2>Connexion Étudiant</h2>
                    <form action="" method="post">
                        <div class="form-field">
                            <label for="apogee">Numéro Apogée</label>
                            <i class="fas fa-id-card icon"></i>
                            <input type="text" name="apogee" id="apogee" placeholder="Entrez votre numéro Apogée" value="<?= htmlspecialchars($apogee) ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="password">Mot de passe</label>
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="password" id="password" placeholder="Entrez votre mot de passe" value="<?= htmlspecialchars($mot_de_passe) ?>" required>
                            <i class="field-icon fas fa-eye-slash toggle-password" onclick="togglePassword('password')"></i>
                        </div>

                        <button type="submit" name="subEtu" class="submit-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Connexion
                        </button>

                        <?= $error ?>
                    </form>
                    <div class="message">
                        Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a>
                    </div>

                <?php elseif ($activeTab === 'admin'): ?>
                    <h2>Connexion Administrateur</h2>
                    <form action="" method="post">
                        <div class="form-field">
                            <label for="id">Identifiant</label>
                            <i class="fas fa-user icon"></i>
                            <input type="text" name="id" id="id" placeholder="Entrez votre identifiant" value="<?= htmlspecialchars($id_administrateur) ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="passwordAd">Mot de passe</label>
                            <i class="fas fa-lock icon"></i>
                            <input type="password" name="password" id="passwordAd" placeholder="Entrez votre mot de passe" value="<?= htmlspecialchars($mot_de_passeAd) ?>" required>
                            <i class="field-icon fas fa-eye-slash toggle-password" onclick="togglePassword('passwordAd')"></i>
                        </div>

                        <button type="submit" name="subAdm" class="submit-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Connexion
                        </button>

                        <?= $errorAd ?>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="theme-btn-container" id="theme-buttons"></div>

    <script>
        // Gestion du loader
        window.addEventListener('DOMContentLoaded', function() {
            // Vérifier si l'utilisateur a déjà vu l'animation
            if (!sessionStorage.getItem('introShown')) {
                setTimeout(function() {
                    const loader = document.getElementById('loader-container');
                    const content = document.getElementById('main-content');
                    
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                        content.style.display = 'block';
                    }, 500);
                    
                    // Marquer comme "déjà vu"
                    sessionStorage.setItem('introShown', 'true');
                }, 2000);
            } else {
                // Ne pas afficher le loader
                document.getElementById('loader-container').style.display = 'none';
                document.getElementById('main-content').style.display = 'block';
            }
        });

        // Basculer l'affichage du mot de passe
        function togglePassword(id) {
            const passwordInput = document.getElementById(id);
            const toggleIcon = document.querySelector(`#${id} + .toggle-password`);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // Thèmes
        const themes = [
            {
                background: "linear-gradient(135deg, #0f3460, #1a1a2e)",
                buttonColor: "#4e8cff",
                buttonBackground: "#4e8cff"
            },
            {
                background: "linear-gradient(135deg, #461220, #8C2130)",
                buttonColor: "#E94560",
                buttonBackground: "#E94560"
            },
            {
                background: "linear-gradient(135deg, #192A51, #114B5F)",
                buttonColor: "#967AA1",
                buttonBackground: "#967AA1"
            },
            {
                background: "linear-gradient(135deg, #1A1A2E, #16213E)",
                buttonColor: "#0F3460",
                buttonBackground: "#0F3460"
            }
        ];

        // Affichage des boutons de thème
        const btnContainer = document.getElementById('theme-buttons');
        
        themes.forEach((theme, index) => {
            const btn = document.createElement('div');
            btn.className = 'theme-btn';
            btn.style.background = theme.buttonBackground;
            btn.addEventListener('click', () => changeTheme(theme));
            btnContainer.appendChild(btn);
        });

        // Changer de thème
        function changeTheme(theme) {
            document.body.style.background = theme.background;
            
            const buttons = document.querySelectorAll('.submit-btn');
            buttons.forEach(btn => {
                btn.style.backgroundColor = theme.buttonColor;
            });
            
            const activeLinks = document.querySelectorAll('.nav-link.active');
            activeLinks.forEach(link => {
                link.style.backgroundColor = theme.buttonColor;
            });
        }
    </script>
</body>
</html>