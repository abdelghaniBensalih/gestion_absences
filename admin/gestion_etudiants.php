<!-- connexion a la base de donnees -->
<?php
$title = "Gestion des etudiants";
    require_once "../includes/header.php";
    require "../config/db.php";
?> 
<!-- voir liste etudiants -->
<?php
if(isset($_POST["vle"])){
$sqle="SELECT 
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

$lignee=$pdo->query($sqle)->fetchAll() ;
echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black;"> <tr><th style="border: 1px solid black; padding: 8px;">apogee</th><th style="border: 1px solid black; padding: 8px;">nom_etudiant</th><th style="border: 1px solid black; padding: 8px;">prenom</th><th style="border: 1px solid black; padding: 8px;">email</th><th style="border: 1px solid black; padding: 8px;">nom_module</th></tr>';
foreach($lignee as $e){
echo '<tr><td style="border: 1px solid black; padding: 8px;">'. $e["apogee"].'</td><td style="border: 1px solid black; padding: 8px;">' . $e["nom_etudiant"].'</td><td style="border: 1px solid black; padding: 8px;">'. $e["prenom"].'</td><td style="border: 1px solid black; padding: 8px;">'.  $e["email"].'</td>td style="border: 1px solid black; padding: 8px;">'. $e["nom_module"].'</td></tr>';
}
echo '</table> ';
}
?>
<!-- suprimer etudiant -->
<?php
if(isset($_POST["se"])){ ?>
<form action="" method="POST">
<input type="number" placeholder="appoge_etudiant" name="nes"><br><br>  
<input type="submit" >
</form>
<?php } ?>
<?php if(isset($_POST["nes"])){ 
$sqlf="delete from etudiants where apogee=(?);";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nes"]]);
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<center><div> 
<form action="" method="POST">
<?php if(!isset($_POST["vle"]) && !isset($_POST["se"])) {?>
    <input type="submit" value="voir_liste_etudiants" name="vle"><br><br>
    <input type="submit" value="suprimer_etudiant" name="se"><br><br>
    <input type="submit" value="modifier_etudiant" name="me"><br><br>
    <?php } ?>
</form>
</div></center>
</body>
</html>