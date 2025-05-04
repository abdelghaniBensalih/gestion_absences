<?php
try {
  $pdo = new PDO("mysql:host=localhost;dbname=gestion_etudiants;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("Erreur de connexion : " . $e->getMessage());
}


/*voir liste filieres */
if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["voir"]))){
    $sql="SELECT DISTINCT nom FROM filieres ";
    $fil=$pdo->query($sql)->fetchAll();
    foreach($fil as $fi ){
        ?>
        <table style="border: 1px solid black;">
        <tr> <td> <?php echo $fi["nom"] ?></td> </tr> </table> 
        <?php
    }
}
?>
<!-- ajouter filiere -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ajouter"])) {
    ?>
<form method ="POST" action=""> <input type="text" name="nf" id="nf">
    <input type="submit" value="ajj" name="ajj">
    </form> 
<?php } ?>
<?php
if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["ajj"]))){
    if(!empty($_POST["nf"])){
    $sql=" insert into filieres(nom) values(?); ";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$_POST["nf"]]);}
        ?>
        <?php
    }
?>



<?php if ($_SERVER["REQUEST_METHOD"] != "POST") { ?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <center> <div>

    <form action="" method="POST">
        <input type="submit" value="voir_filiere" name="voir" style="width:10%; padding:10px;">
        <br><br>

        <input type="submit" value="ajouter_filiere" name="ajouter" style="width:10%; padding:10px;">
        <br><br>

        <input type="submit" value="modifier_filiere" name="modifier" style="width:10%; padding:10px;">
        <br><br>

        <input type="submit" value="suprimer_filiere" name="suprimer" style="width:10%; padding:10px;">
        <br><br>

    </form> 
    </div></center> 
</body>
<?php } ?>