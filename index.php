<?php
$title = "Accueil";
require_once "includes/header.php";
require "config/db.php";

header('Content-Type: text/html; charset=UTF-8');

$error = "";
$errorAd = "";
$activeTab = isset($_POST['admin']) || isset($_POST['subAdm']) ? 'admin' : 'etudiant';
if (isset($_POST['subEtu'])) {
    $nom = $_POST['apogee'];
    $password = $_POST['password'];
    $tmp = false;
    foreach ($lignes as $ligne) {
        if ($ligne['apogee'] == $nom && $ligne['mot_de_passe'] == $password) {
            $tmp = true;
        }
    }
    if ($tmp) {
        $error = "";
        header("location:dashbord_etudiant.php");
        exit();
    } else {
        $error = "<p style='color:red'>le mot de passe et/ou Apogee est incorrect</p>";
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
        $errorAd = "";
        header("location:dashbord_admin.php");
        exit();
    } else {
        $errorAd = "<p style='color:red'>le mot de passe et/ou identifiant est incorrect</p>";
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
    </style>
</head>
<body class="bg-light">
    <center>
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
                    <input type="submit" id="btn" name="subEtu" value="Envoyer">
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
                    <input type="password" name="password" id="passwordgraveyard2013-02-10T22:37:04+00:00passwordAd" placeholder="Mot de passe"><br>
                    <input type="submit" name="subAdm" id="btn" value="Envoyer">
                    <?= $errorAd ?>
                </form>
            </div>
        <?php endif ?>
    </center>
</body>
</html>