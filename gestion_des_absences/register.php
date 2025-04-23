<?php
$title = "Inscription";
require_once "includes/header.php";
require "config/db.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <style>
    .btns3 {
      width: 600px;
      height: 70vh;
      border: solid 2px #0df;
      border-radius: 5px;
      line-height: 50px;
      margin: 20px;
      margin-top: 50px;
      background-color: rgba(241, 241, 267);
      box-shadow: 40px 60px 30px #d3d3d3;
    }

    .btns3 input {
      width: 200px;
      height: 30px;
      border: solid 1px black;
      border-radius: 7px;
      text-indent: 5px;
    }

    #filiere {
      width: 200px;
      height: 30px;
      border: solid 1px black;
      border-radius: 7px;
    }

    #btn {
      width: 200px;
      height: 30px;
      border: solid 1px black;
      border-radius: 7px;
      color: blue;
      font-family: Georgia, 'Times New Roman', Times, serif;
      font-size: x-large;
      background-color: #0df;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <center>
    <div class="btns3">
      <h1>Bienvenue 😍</h1>
      <form action="" method="post">
        <label for="nom">Nom </label>
        <?php for ($i = 0; $i <= 13; $i++): ?>
          &nbsp;
        <?php endfor ?>
        <input type="text" name="nom" id="nom" required><br>

        <label for="prenom">Prenom </label>
        <?php for ($i = 0; $i <= 11; $i++): ?>
          &nbsp;
        <?php endfor ?>
        <input type="text" name="prenom" id="prenom" required><br>

        <label for="apogee">Numero Apogee</label>
        <?php for ($i = 0; $i <= 4; $i++): ?>
          &nbsp;
        <?php endfor ?>
        <input type="number" name="apogee" id="apogee" required><br>

        <label for="email">Email</label>
        <?php for ($i = 0; $i <= 12; $i++): ?>
          &nbsp;
        <?php endfor ?>
        <input type="email" name="email" id="email" required><br>

        <label for="password">Mot de Passe</label>
        <?php for ($i = 0; $i <= 6; $i++): ?>
          &nbsp;
        <?php endfor ?>
      <input type="password" name="password" id="password" required ><br>
      

      <label for="filiere">Filiere </label>
      <?php for($i=0;$i<=12;$i++): ?>
        &nbsp;
        <?php endfor ?>
      <select name="filiere" id="filiere">
       <option value="">------</option>
<<<<<<< HEAD
       <?php foreach($dd as $ligne){?>
        <option value="">$ligne['nom_filiere']</option>
      <?php }?>
      </select><br>
      <input type="submit" name='subEtu' id="btn" value="Envoyer">
=======
       <?php
        try{
          $pdo=new PDO("mysql:host=localhost;dbname=gestion_etudiants;charset=utf8mb4","root","",[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]);
          echo "<p>Connexion réussie !</p>";
        }catch (PDOException $e) {
          die("Erreur de connexion : " . $e->getMessage());      
        }
        $sql="select * from filieres ";
        $lignes=$pdo->query($sql)->fetchAll();
       foreach($lignes as $ligne){?>
        <option value=""><?= $ligne['nom']?></option>
       <?php }?>
      </select>
      <input type="submit" name='subEtu' value="Envoyer">
>>>>>>> fa55cf813d9eb83fdb90b73bbbb9c0487cdcdc68
   </form>

      <p>vous avez déjà inscrire? <a href="index.php">se connecte</a></p>
    </div>
  </center>
</body>

</html>

<?php
if (isset($_POST['subEtu'])) {
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $apogee = $_POST['apogee'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $filiere = $_POST['filiere'];
  $id_filiere = null;
  $id_module = null;

  foreach ($lignesF as $ligne) {
    if ($ligne['nom'] == $filiere) {
      $id_filiere = $ligne['id_filiere'];
    }
  }

  foreach ($lignesM as $ligne2) {
    if ($ligne2['id_filiere'] == $id_filiere) {
      $id_module = $ligne2['id_module'];
    }
  }

$sql1 = "INSERT INTO etudiants VALUES(?,?,?,?,?,?)";
$stmt = $pdo->prepare($sql1);
$stmt->execute([$apogee,$nom,$prenom,$email,$password,$id_filiere]);
}
?>