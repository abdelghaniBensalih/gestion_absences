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

        <form action="" method="post">
            <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-primary rounded-5 shadow-sm" id="pillNav2" role="tablist" style="--bs-nav-link-color: var(--bs-white); --bs-nav-pills-link-active-color: var(--bs-primary); --bs-nav-pills-link-active-bg: var(--bs-white);">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'etudiant' ? 'active' : '' ?> rounded-5" name="etudiant" type="submit" role="tab" aria-selected="<?= $activeTab === 'etudiant' ? 'true' : 'false' ?>">Étudiant(e)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'admin' ? 'active' : '' ?> rounded-5" name="admin" type="submit" role="tab" aria-selected="<?= $activeTab === 'admin' ? 'true' : 'false' ?>">Administrateur</button>
                </li>
            </ul>
        </form>

        <?php if ($activeTab === 'etudiant'): ?>
            <div class="etudiantConnecte">
                <form action="" method="post">
                    <label for="apogee">Entrer votre APOGEE: </label><br>
                    <input type="text" name="apogee" id="apogee" placeholder="Numéro Apogée"><br>
                    <label for="password">Entrer votre Mot de passe: </label><br>
                    <input type="password" name="password" id="password" placeholder="Mot de passe"><br>
                    <!-- <input type="submit" id="btn" name="subEtu" value="Envoyer"> -->
                    <button type="submit" class="btn btn-outline-primary" name="subEtu"  value="Envoyer">Envoyer</button>
                    <?= $error ?>
                </form>
                <p>Vous n'êtes pas encore inscrit ? <a href="register.php">Inscription</a></p>
            </div>
        <?php endif ?>

        <?php if ($activeTab === 'admin'): ?>
            <div class="adminConnecte">
                <form action="" method="post">
                    <label for="id">Entrer votre identifiant: </label><br>
                    <input type="text" name="id" id="id" placeholder="Nom d'utilisateur"><br>
                    <label for="passwordAd">Entrer votre Mot de passe: </label><br>
                    <input type="password" name="password" id="passwordAd" placeholder="Mot de passe"><br>
                    <!-- <input type="submit" name="subAdm" id="btn" value="Envoyer"> -->
                    <button type="submit" class="btn btn-outline-primary" name="subAdm"  value="Envoyer">Envoyer</button>
                    <?= $errorAd ?>

                </form>
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

</body>
</html>