<?php
$title = "Accueil";
require_once "includes/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <center> <div>

    <form action="admin/gestion_filieres.php" method="POST">
        <input type="submit" value="gestion_filiere" style="width:10%; padding:10px;">
    </form><br><br>

    <form action="admin/gestion_modules.php" method="POST">
        <input type="submit" value="gestion_modules" style="width:10%; padding:10px;">
    </form><br><br>

    <form action="admin/gestion_etudiants.php" method="POST">
        <input type="submit" value="gestion_etudiants" style="width:10%; padding:10px;">
    </form><br><br>

    <form action="admin/gestion_absences.php" method="POST">
        <input type="submit" value="gestion_absences" style="width:10%; padding:10px;">
    </form>

    </div></center>
</body>
</html>