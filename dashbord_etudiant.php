<?php
session_start();
if($_SESSION['auth'] != "Oui"){
    header("location:index.php");
    exit();
}
require "config/db.php";
$title="Espace Etudiant";
require "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    foreach($lignesDashE as $ligne){
        $name = $ligne['nom'];
        $name2 = $ligne['prenom'];
    }
    ?>
    <h1>Welcome <?= $name." ". $name2 ?></h1>
    <form action="" method="post">
    <button type="submit" name="supp" value="supp">Supprimer Mon Compte</button>
    </form>
</body>
</html>
<?php
if(isset($_POST['supp'])){
    $sqlSupp = "DELETE FROM etudiants WHERE apogee = ".$lignesDashE['apogee'];
    $pdo->exec($sqlSupp);
    // $pdo->query($sqlSupp);
    session_destroy();
    header("location:index.php");
}
?>



