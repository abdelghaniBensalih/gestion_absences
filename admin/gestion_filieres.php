<!-- connexion a la base de donnees -->
<?php
try{
$pdo=new PDO ("mysql:host=localhost;dbname=gestion_etudiants","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}
catch(PDOException $e){
echo $e->getMessage();
}
?> 
<!-- voir liste des filieres -->
<?php
if(isset($_POST["vlf"])){
$sqlf="SELECT 
    f.nom AS nom_filiere,
    m.nom AS nom_module
FROM 
    filieres f
JOIN 
    modules m ON f.id_filiere = m.id_filiere
ORDER BY 
    f.nom, m.nom;
";
$lignef=$pdo->query($sqlf)->fetchAll() ;
echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black;">'.'<tr><th style="border: 1px solid black; padding: 8px;"> nom_filiere</th><th style="border: 1px solid black; padding: 8px;"> nom_module</th></tr>';
foreach($lignef as $f){
echo '<tr><td style="border: 1px solid black; padding: 8px;">'. $f["nom_filiere"].'</td><td style="border: 1px solid black; padding: 8px;">'. $f["nom_module"].'</td></tr>';
}
echo '</table>';
}
?>
<!-- ajouter filier -->
<?php
if(isset($_POST["af"])){ ?>
<form action="" method="POST">
<input type="text"  placeholder="nom_filiere" name="nf"><br><br>  
<input type="submit" >
</form>
<?php } ?>
<?php if(isset($_POST["nf"])){ 
$sqlf="insert into  filieres(nom) values (?);";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nf"]]);
} 
?>
<!-- suprimer filiere -->
<?php
if(isset($_POST["sf"])){ ?>
<form action="" method="POST">
<input type="text" placeholder="nom_filiere" name="nfs"><br><br>  
<input type="submit" >
</form>
<?php } ?>
<?php if(isset($_POST["nfs"])){ 
$sqlf="delete from filieres where nom=(?);";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nfs"]]);
} 
?>
<!-- mettre à jour une filière -->
<?php
if(isset($_POST["mf"])){ ?>
<form action="" method="POST">
<input type="text" placeholder="ancien_nom_filiere" name="anf"><br><br>
<input type="text" placeholder="le nom modifier" name="nnf"><br><br>
<input type="submit" >
</form>
<?php } ?>
<?php if(isset($_POST["anf"])){
$sqlf="UPDATE filieres SET nom = ? WHERE nom = ?;";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nnf"], $_POST["anf"]]);
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
<?php if(!isset($_POST["vlf"]) && !isset($_POST["af"]) && !isset($_POST["sf"]) && !isset($_POST["mf"]) ) {?>
    <input type="submit" value=" liste_filiere et module_associees" name="vlf"><br><br>
    <input type="submit" value="ajouter_filiere" name="af"><br><br>
    <input type="submit" value="suprimer_filiere" name="sf"><br><br>
    <input type="submit" value="modifier_filiere" name="mf"><br><br>
    <?php } ?>
</form>
</div></center>
</body>
</html>