<?php
session_start();



$title = "Accueil";
require_once "includes/header.php";
require "config/db.php";

header('Content-Type: text/html; charset=UTF-8');

// $_SESSION['auth'] = "Non";
// $_SESSION['authAdmin'] = "Non";
$error = "";
$errorAd = "";
$activeTab = isset($_POST['admin']) || isset($_POST['subAdm']) ? 'admin' : 'etudiant';

if (isset($_POST['subEtu'])) {
    $nom = $_POST['apogee'];
    $password = $_POST['password'];
    $_SESSION['apogee']=$nom;
    $tmp = false;
    foreach ($lignes as $ligne) {
        if ($ligne['apogee'] == $nom && password_verify($password,$ligne['mot_de_passe'])) {
            $tmp = true;
        }
    }
    if ($tmp) {
        $_SESSION['auth'] = "Oui";
        $error = "";
        header("location:dashbord_etudiant.php");
        exit();
    } else {
        // $error = "<p style='color:red'>le mot de passe et/ou Apogee est incorrect</p>";
        $error = <<<_HTML
            <div class="alert alert-danger" role="alert">
                <strong>Erreur !</strong> Le mot de passe et/ou Apogee est incorrect.
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
        if ($ligne['id_administrateur'] == $nom && $ligne['mot_de_passe'] == $password) {
            $Tmp = true;
        }
    }
    if ($Tmp) {
        $_SESSION['authAdmin'] = "Oui";
        $errorAd = "";
        header("location:dashbord_admin.php");
        exit();
    } else {
        // $errorAd = "<p style='color:red'>le mot de passe et/ou identifiant est incorrect</p>";
        $errorAd = <<<_HTML
            <div class="alert alert-danger" role="alert">
                <strong>Erreur !</strong> Le mot de passe et/ou identifiant est incorrect.
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
    <title>Accueil</title>
    <link rel="stylesheet" href="assetss/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <style>
.container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: #ededed;
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
  animation-delay: -0.15s;
}

circle:nth-of-type(2) {
  stroke-dasharray: 500px;
  animation-delay: -0.30s;
}

circle:nth-of-type(3) {
  stroke-dasharray: 450px;
  animation-delay: -0.45s;
}

circle:nth-of-type(4) {
  stroke-dasharray: 300px;
  animation-delay: -0.60s;
}

@keyframes preloader {
  50% {
    transform: rotate(360deg);
  }
}

#main-content {
  display: none;
}
/* ############################################## */

:root {
    --background: #1a1a2e;
    --color: #ffffff;
    --primary-color: #0f3460;
}

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    box-sizing: border-box;
    font-family: "poppins";
    background: var(--background);
    color: var(--color);
    letter-spacing: 1px;
    transition: background 0.2s ease;
    -webkit-transition: background 0.2s ease;
    -moz-transition: background 0.2s ease;
    -ms-transition: background 0.2s ease;
    -o-transition: background 0.2s ease;
}

a {
    text-decoration: none;
    color: var(--color);
}

h1 {
    font-size: 2.5rem;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    position: relative;
    width: 22.2rem;
}
.form-container {
    border: 1px solid hsla(0, 0%, 65%, 0.158);
    box-shadow: 0 0 36px 1px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    backdrop-filter: blur(20px);
    z-index: 99;
    padding: 2rem;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
}

.login-container form input {
    display: block;
    padding: 14.5px;
    width: 100%;
    margin: 2rem 0;
    color: var(--color);
    outline: none;
    background-color: #9191911f;
    border: none;
    border-radius: 5px;
    font-weight: 500;
    letter-spacing: 0.8px;
    font-size: 15px;
    backdrop-filter: blur(15px);
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    -ms-border-radius: 5px;
    -o-border-radius: 5px;
}

.login-container form input:focus {
    box-shadow: 0 0 16px 1px rgba(0, 0, 0, 0.2);
    animation: wobble 0.3s ease-in;
    -webkit-animation: wobble 0.3s ease-in;
}

.login-container form button {
    background-color: var(--primary-color);
    color: var(--color);
    display: block;
    padding: 13px;
    border-radius: 5px;
    outline: none;
    font-size: 18px;
    letter-spacing: 1.5px;
    font-weight: bold;
    width: 100%;
    cursor: pointer;
    margin-bottom: 2rem;
    transition: all 0.1s ease-in-out;
    border: none;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    -ms-border-radius: 5px;
    -o-border-radius: 5px;
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition: all 0.1s ease-in-out;
    -ms-transition: all 0.1s ease-in-out;
    -o-transition: all 0.1s ease-in-out;
}

.login-container form button:hover {
    box-shadow: 0 0 10px 1px rgba(0, 0, 0, 0.15);
    transform: scale(1.02);
    -webkit-transform: scale(1.02);
    -moz-transform: scale(1.02);
    -ms-transform: scale(1.02);
    -o-transform: scale(1.02);
}

.circle {
    width: 8rem;
    height: 8rem;
    background: var(--primary-color);
    border-radius: 50%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    -o-border-radius: 50%;
    position: absolute;
}

.illustration {
    position: absolute;
    top: -14%;
    right: -2px;
    width: 90%;
}

.circle-one {
    top: 0;
    left: 0;
    z-index: -1;
    transform: translate(-45%, -45%);
    -webkit-transform: translate(-45%, -45%);
    -moz-transform: translate(-45%, -45%);
    -ms-transform: translate(-45%, -45%);
    -o-transform: translate(-45%, -45%);
}

.circle-two {
    bottom: 0;
    right: 0;
    z-index: -1;
    transform: translate(45%, 45%);
    -webkit-transform: translate(45%, 45%);
    -moz-transform: translate(45%, 45%);
    -ms-transform: translate(45%, 45%);
    -o-transform: translate(45%, 45%);
}

.register-forget {
    margin: 1rem 0;
    display: flex;
    justify-content: space-between;
}
.opacity {
    opacity: 0.6;
}

.theme-btn-container {
    position: absolute;
    left: 0;
    bottom: 2rem;
}

.theme-btn {
    cursor: pointer;
    transition: all 0.3s ease-in;
}

.theme-btn:hover {
    width: 40px !important;
}

@keyframes wobble {
    0% {
        transform: scale(1.025);
        -webkit-transform: scale(1.025);
        -moz-transform: scale(1.025);
        -ms-transform: scale(1.025);
        -o-transform: scale(1.025);
    }
    25% {
        transform: scale(1);
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -o-transform: scale(1);
    }
    75% {
        transform: scale(1.025);
        -webkit-transform: scale(1.025);
        -moz-transform: scale(1.025);
        -ms-transform: scale(1.025);
        -o-transform: scale(1.025);
    }
    100% {
        transform: scale(1);
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -o-transform: scale(1);
    }
}

</style>

</head>
<body class="bg-light">
<!-- <div id="loader">
  <video autoplay muted>
    <source src="assetss/animation.webm" type="video/webm">
    Votre navigateur ne supporte pas les vidéos HTML5.
  </video>
</div> -->
<center>
    <div id="container">
      
      <svg class="loader" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 340">
         <circle cx="170" cy="170" r="160" stroke="#E2007C"/>
         <circle cx="170" cy="170" r="135" stroke="#404041"/>
         <circle cx="170" cy="170" r="110" stroke="#E2007C"/>
         <circle cx="170" cy="170" r="85" stroke="#404041"/>
      </svg>
      
    </div>
</center>
<center id="main-content">
<div class="mx-auto" style="max-width: 400px;">
        <form action="" method="post">
            <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-success rounded-5 shadow-sm" id="pillNav2" role="tablist" style="--bs-nav-link-color: var(--bs-white); --bs-nav-pills-link-active-color: var(--bs-primary); --bs-nav-pills-link-active-bg: var(--bs-white);">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'etudiant' ? 'active' : '' ?> rounded-5" name="etudiant" type="submit" role="tab" aria-selected="<?= $activeTab === 'etudiant' ? 'true' : 'false' ?>">Étudiant(e)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'admin' ? 'active' : '' ?> rounded-5" name="admin" type="submit" role="tab" aria-selected="<?= $activeTab === 'admin' ? 'true' : 'false' ?>">Administrateur</button>
                </li>
            </ul>
        </form>
</div>
        <?php if ($activeTab === 'etudiant'): ?>
            <!-- <div class="etudiantConnecte">
                <form action="" method="post">
                    <label for="apogee">Entrer votre APOGEE: </label><br>
                    <input type="text" name="apogee" id="apogee" placeholder="Numéro Apogée"><br>
                    <label for="password">Entrer votre Mot de passe: </label><br>
                    <input type="password" name="password" id="password" placeholder="Mot de passe"><br>
                    <button type="submit" class="btn btn-outline-primary" name="subEtu"  value="Envoyer">Envoyer</button>
                    <?= ""//$error ?>
                </form>
                <p>Vous n'êtes pas encore inscrit ? <a href="register.php">Inscription</a></p>
            </div> -->

            <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <img src="https://raw.githubusercontent.com/hicodersofficial/glassmorphism-login-form/master/assets/illustration.png" alt="illustration" class="illustration" />
                <h1 class="opacity">LOGIN</h1>
                <form action="" method="post">
                    <input type="text" name="apogee" placeholder="APOGEE" />
                    <input type="password" name="password" placeholder="PASSWORD" />
                    <button name="subEtu" type="submit" class="opacity" value="Envoyer">SUBMIT</button>
                    <?= $error ?>
                </form>
                <div class="register-forget opacity">
                <a href="register.php">REGISTER</a>
                    <a href="">FORGOT PASSWORD</a>
                </div>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
        <?php endif ?>

        <?php if ($activeTab === 'admin'): ?>
            <!-- <div class="adminConnecte"> -->
                <!-- <form action="" method="post">
                    <label for="id">Entrer votre identifiant: </label><br>
                    <input type="text" name="id" id="id" placeholder="Nom d'utilisateur"><br>
                    <label for="passwordAd">Entrer votre Mot de passe: </label><br>
                    <input type="password" name="password" id="passwordAd" placeholder="Mot de passe"><br>
                    <button type="submit" class="btn btn-outline-primary" name="subAdm"  value="Envoyer">Envoyer</button>
                    <?= "" //$errorAd ?>

                </form> -->
      <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <img src="https://raw.githubusercontent.com/hicodersofficial/glassmorphism-login-form/master/assets/illustration.png" alt="illustration" class="illustration" />
                <h1 class="opacity">LOGIN</h1>
                <form action="" method="post">
                    <input type="text" name="id" placeholder="USERNAME" />
                    <input type="password" name="password" placeholder="PASSWORD" />
                    <button type="submit" name="subAdm" class="opacity" value="Envoyer">SUBMIT</button>
                    <?= $errorAd ?>
                </form>
                <!-- <div class="register-forget opacity">
                    <a href="register.php">REGISTER</a>
                    <a href="">FORGOT PASSWORD</a>
                </div> -->
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
    </div>
        <?php endif ?>
    </center>





    <script>

  // Si l'utilisateur n'a pas encore vu l'animation (dans cette session)
  if (!sessionStorage.getItem('introShown')) {
    setTimeout(() => {
      document.getElementById('container').style.display = 'none';
      document.getElementById('main-content').style.display = 'block';
    }, 3000);
    // Marquer comme "déjà vu"
    sessionStorage.setItem('introShown', 'true');
  } else {
    // Ne pas afficher le container
    document.getElementById('container').style.display = 'none';
    document.getElementById('main-content').style.display = 'block';
  }
</script>


<script>
  const themes = [
    {
        background: "#1A1A2E",
        color: "#FFFFFF",
        primaryColor: "#0F3460"
    },
    {
        background: "#461220",
        color: "#FFFFFF",
        primaryColor: "#E94560"
    },
    {
        background: "#192A51",
        color: "#FFFFFF",
        primaryColor: "#967AA1"
    },
    {
        background: "#F7B267",
        color: "#000000",
        primaryColor: "#F4845F"
    },
    {
        background: "#F25F5C",
        color: "#000000",
        primaryColor: "#642B36"
    },
    {
        background: "#231F20",
        color: "#FFF",
        primaryColor: "#BB4430"
    }
];

const setTheme = (theme) => {
    const root = document.querySelector(":root");
    root.style.setProperty("--background", theme.background);
    root.style.setProperty("--color", theme.color);
    root.style.setProperty("--primary-color", theme.primaryColor);
    root.style.setProperty("--glass-color", theme.glassColor);
};

const displayThemeButtons = () => {
    const btnContainer = document.querySelector(".theme-btn-container");
    themes.forEach((theme) => {
        const div = document.createElement("div");
        div.className = "theme-btn";
        div.style.cssText = `background: ${theme.background}; width: 25px; height: 25px`;
        btnContainer.appendChild(div);
        div.addEventListener("click", () => setTheme(theme));
    });
};

displayThemeButtons();

</script>

</body>
</html>