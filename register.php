<?php
$title = "Inscription";
$success = false;
require_once "includes/header.php";
require "config/db.php";

?>


<?php
  if (isset($_POST['subEtu'])) {
  try{

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $apogee = $_POST['apogee'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    // $filiere = $_POST['filiere'];
    $id_filiere = $_POST['filiere'];
    $id_module = null;
    $success = false;


    foreach ($lignesM as $ligne2) {
      if ($ligne2['id_filiere'] == $id_filiere) {
        $id_module = $ligne2['id_module'];
      }
    }


  $sql1 = "INSERT INTO etudiants VALUES(?,?,?,?,?,?)";
  $stmt = $pdo->prepare($sql1);
  $success = $stmt->execute([$apogee,$nom,$prenom,$email,$passwordHash,$id_filiere]);
  }catch(Exception $e){
    $Erreur = <<< EOT
            <div class="alert alert-danger py-1 m-2" role="alert" style="font-size: 0.9rem; padding: 0.25rem 0.5rem;">
    <strong>Erreur !</strong> compte dèja exciste.
    </div>
EOT;
  }
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
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
      /* box-shadow: 40px 60px 30px #d3d3d3; */
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
      height: 45px;
      border: solid 1px black;
      border-radius: 7px;
      color: blue;
      font-family: Georgia, 'Times New Roman', Times, serif;
      font-size: x-large;
      background-color: #0df;
      cursor: pointer;
    }
    .btn{
      border-radius: 50%;
      color:white;
    }
    #toright{
      position: fixed;
      top: 90vh;
      right: 0;
      padding: 20px;
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
       <?php foreach($lignesF as $ligne){?>
        <option value="<?= $ligne['id_filiere'] ?>"><?=$ligne['nom']?></option>
      <?php }?>
      </select><br>
      <input type="submit" name='subEtu' id="btn" value="Envoyer">
      <?php if($success): ?>
        <div class="alert alert-success py-1 m-2" role="alert" style="font-size: 0.9rem; padding: 0.25rem 0.5rem;">
    <strong>Succès !</strong> vous êtes inscrire.
    </div>
    <?php else: ?>
      <?php if(isset($Erreur)): ?>
        <?= $Erreur ?>
      <?php endif; ?>
    <?php endif; ?>
   </form>
      <p>vous avez déjà inscrire? <a href="index.php">se connecte</a></p>
    </div>
  </center>




<div id="toright" >

  <button class="btn" style="background-color: white;color:black;"   onclick="changeColor('white')">White</button>
  <button class="btn" style="background-color: black;"   onclick="changeColor('black')">Black</button>
  <button class="btn" style="background-color: gray;"    onclick="changeColor('gray')">Gray</button>

</div>  


<script>
          function changeColor(colorName) {
    document.body.style.background = colorName;
}
</script>



</body>
</html>
