<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_etudiants;charset=utf8mb4", "root", "", [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
  }
  
?>
 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestion des filieres</title>
</head>
<body>
    <center>
        <div class="container">
           <h2>select une operation</h2>
           <form action="" method="POST">
               <button type="submit" name="a">ajout une filiere</button>
               <button type="submit" name="m">mise a jour </button>
               <button type="submit" name="s">supprime une filiere</button>
           </form>
        </div>
        <div class="resultat">
            <?php if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST["a"])){?>
                <form action="">
                    <input type="text" name="nom" placeholder="entre nom"  required>
                    <input type="submit" value="envoyer">
                </form>
              
            <?php 
                if (!empty($_POST['nom'])) {
                    $nom = htmlspecialchars($_POST['nom']);
                    $sqlgfilier = "insert into filieres(nom) values(:nom)";
                    $stmt = $pdo->prepare($sqlgfilier);
                    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                    $lignegf = $stmt->execute(); 
                    echo $lignegf ? "Filière ajoutée avec succès." : "Erreur lors de l'ajout.";
                }
            }elseif(isset($_POST["m"])){
                ?>
                <form action="">
                    <input type="text" name="nom" placeholder="entre nom"  required>
                    <input type="number" name="id" placeholder="entre id"  required>
                    <input type="submit" value="envoyer">
                </form>
                
                <?php if (!empty($_POST['nom'])&& !empty($_POST['id'])) {
                    $nom = htmlspecialchars($_POST['nom']);
                    $id = ($_POST['id']);
                    $sqlgfilier = "update filieres set nom = :nom where id = :id";
                    $stmt = $pdo->prepare($sqlgfilier);
                    $lignegf = $stmt->execute();
                    echo $lignegf ? "le mis à jour a fait." : "Erreur lors de mis à jour!!.";
                } }elseif(isset($_POST["s"])){ ?>
                <form action="">
                    <input type="number" name="id" placeholder="entre id"  required>
                    <input type="text" name="nom" placeholder="entre nom"  required>
                    <input type="submit" value="envoyer">
                </form>
                <?php } if(!empty($_POST['id']) && !empty($_POST['id'])){
                    $id = ($_POST['id']);
                    $nom = htmlspecialchars($_POST['nom']);
                    $sqlgfilier = "delete from filieres where id = :id and nom = :nom";
                    $stmt = $pdo->prepare($sqlgfilier);
                    $lignegf = $stmt->execute();
                    echo $lignegf ? "Filière supprimée avec succès." : "Erreur lors de la suppression.";
                }    ?>







            <?php } ?>
            
        </div>
    </center>

    
</body>
</html>