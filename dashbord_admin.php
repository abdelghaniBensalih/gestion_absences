<?php
session_start();
if($_SESSION['authAdmin'] != "Oui"){
    header("location:index.php");
    exit();
}
require "config/db.php";
$title="Espace Administrateur";
require "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace administrateur</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #333;
        }
        .container {
            margin-top: 50px;
        }
        .container form {
            margin-bottom: 20px;
            color : #457b9d;
            font-size: 20px;
        }
        .resultat {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Welcome Mr.<?= $lignesAd['0']['nom'] ?></h1>
    <center>
        <div class="container">
            <form action="" method="POST">
                <button type="submit" name="g_a">gestion_absences</button>
            </form>


            <form action="" method="POST">
                <button type="submit" name="g_e">gestion_etudiants</button>
            </form>


            <form action="" method="POST">
                <button type="submit" name="g_f">gestion_filieres</button>
            </form>


            <form action="" method="POST">
                <button type="submit" name="g_m">gestion_modules</button>
            </form>

        </div>
        <div class="resultat">
            <?php
                if (isset($_POST['g_a'])) {
                    header("location:admin/gestion_absences.php");
                    exit();
                } elseif (isset($_POST['g_e'])) {
                    header("location:admin/gestion_etudiants.php");
                    exit();
                } elseif (isset($_POST['g_f'])) {
                    header("location:admin/gestion_filieres.php");
                    exit();
                } elseif (isset($_POST['g_m'])) {
                    header("location:admin/gestion_modules.php");
                    exit();
                }
            ?>
        </div>
    </center>
    
</body>
</html>