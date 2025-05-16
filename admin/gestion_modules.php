<!-- connexion a la base de donnees -->
<?php
$title = "Gestion des modules";
    require_once "../includes/header.php";
    require "../config/db.php";
?> 
<?php
if (isset($_POST["vlm"])) {
    $sqlf = "SELECT id_module, nom, nom_responsable, id_filiere ,semistre  FROM modules;";
    $lignef = $pdo->query($sqlf)->fetchAll();
    echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid black;">';
    echo '<tr>
            <th style="border: 1px solid black; padding: 8px;">id_module</th>
            <th style="border: 1px solid black; padding: 8px;">nom_module</th>
            <th style="border: 1px solid black; padding: 8px;">nom_responsable</th>
            <th style="border: 1px solid black; padding: 8px;">id_filiere</th>
            <th style="border: 1px solid black; padding: 8px;">semistre</th>
          </tr>';
    foreach ($lignef as $f) {
        echo '<tr>
                <td style="border: 1px solid black; padding: 8px;">' . $f["id_module"] . '</td>
                <td style="border: 1px solid black; padding: 8px;">' . $f["nom"] . '</td>
                <td style="border: 1px solid black; padding: 8px;">' . $f["nom_responsable"] . '</td>
                <td style="border: 1px solid black; padding: 8px;">' . $f["id_filiere"] . '</td>
                <td style="border: 1px solid black; padding: 8px;">' . $f["semistre"] . '</td>
              </tr>';
    }
    echo '</table>';
}
?>

<!-- ajouter module -->
<?php
if(isset($_POST["am"])){ ?>
<form action="" method="POST">
<input type="text" placeholder="nom_du_module" name="n_m"><br><br>  
<input type="text" placeholder="nom_du_responsable"  name="nr"><br><br>  
<input type="number" placeholder="id_filiere_associees"  name="if"><br><br>  
<select name="sem" required>
    <option value="s1">S1</option>
    <option value="s2">S2</option>
</select><br><br>  
<input type="submit" name="nm" >
</form>
<?php } ?>
<?php if(isset($_POST["nm"])){ 
$sqlf="insert into  modules(nom ,nom_responsable ,id_filiere , semistre) values (?,?,?,?);";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["n_m"],$_POST["nr"],$_POST["if"],$_POST["sem"]]);
} 
?>
<!-- suprimer module -->
<?php
if(isset($_POST["sm"])){ ?>
<form action="" method="POST">
<input type="number" placeholder="id_module" name="nms"><br><br>  
<input type="submit" >
</form>
<?php } ?>
<?php if(isset($_POST["nms"])){ 
$sqlf="delete from modules where id_module=(?);";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nms"]]);
} 
?>
<!-- mettre à jour un module -->
<?php
if(isset($_POST["mm"])){ ?>
<form action="" method="POST">
<input type="text" placeholder="ancien_nom_module" name="anm"><br><br>
<input type="text" placeholder="le nom module modifier" name="nnm"><br><br>
<input type="text" placeholder="ancien_nom de responsable_module" name="anr"><br><br>
<input type="text" placeholder="le nom responsable modifier" name="nnr"><br><br>
<input type="submit" name="bakhta" >
</form>
<?php } ?>
<?php if(isset($_POST["bakhta"])){
$sqlf="UPDATE modules SET nom = ?,nom_responsable = ? WHERE nom = ? and nom_responsable = ?;";
$stmt=$pdo->prepare($sqlf);
$stmt->execute([$_POST["nnm"] ,$_POST["nnr"] , $_POST["anm"], $_POST["anr"]]);
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
<?php if(!isset($_POST["vlm"]) && !isset($_POST["am"]) && !isset($_POST["sm"]) && !isset($_POST["mm"]) ) {?>
    <input type="submit" value="voir_liste_module" name="vlm"><br><br>
    <input type="submit" value="ajouter_module" name="am"><br><br>
    <input type="submit" value="suprimer_modulre" name="sm"><br><br>
    <input type="submit" value="modifier_modulre" name="mm"><br><br>
    <?php } ?>
</form>
</div></center>
</body>
</html>