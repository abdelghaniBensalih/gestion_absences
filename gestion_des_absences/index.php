<?php
$title = "Accueil";
require_once "includes/header.php";
require "config/db.php";

header('Content-Type: text/html; charset=UTF-8');




$error = "";
$formuleAffiche = false;
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
        $formuleAffiche = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="assetss/style.css">

    <style>
    </style>
</head>

<body>
    <center>

        <div>
            <div class="btns1">
                <form action="" method="post">
                    <input type="submit" name='etudiant' value="Etudiant(e)">

                    <input type="submit" name='admin' value="Admin">
                </form>
            </div>


            <?php if (isset($_POST['etudiant']) || $formuleAffiche): ?>
                <div class="etudiantConnecte">
                    <form action="" method="post">
                        <label for="ap">Entrer votre APOGEE: </label><br>
                        <input type="text" name="apogee" id="ap" placeholder='apogee'><br>
                        <label for="pw">Entrer votre Mot de passe: </label><br>
                        <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
                        <input type="submit" id="btn" name='subEtu' value="Envoyer">
                        <?= $error ?>
                    </form>
                    <p>vous avez pas encore inscrire? <a href="register.php">inscription</a></p>
                </div>
            <?php endif ?>
            <?php if (isset($_POST['admin'])): ?>
                <div class="adminConnecte">
                    <form action="" method="post">
                        <label for="ap">Entrer votre identifiant: </label><br>
                        <input type="text" name="id" id="ap" placeholder='username'><br>
                        <label for="pw">Entrer votre Mot de passe: </label><br>
                        <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
                        <input type="submit" name='subAdm' id="btn" value="Envoyer">
                    </form>
                </div>
            <?php endif ?>
        </div>
    </center>
</body>

</html>