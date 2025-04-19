<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Accueil</title>
 <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <center>

   <div >
    <h2><marquee behavior="ltr" direction="">Acceuil</marquee></h2>
    <form action="" method="post">
     <input type="submit" name='etudiant' value="Etudiant(e)">

     <input type="submit" name='admin' value="Admin">
     </form>
   
    <?php if(isset($_POST['etudiant'])):?>
     <form action="" method="post">
      <label for="ap">Entrer votre APOGEE: </label>
      <input type="text" name="apogee" id="ap" placeholder='username'><br>
      <label for="pw">Entrer votre Mot de passe: </label>
      <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
      <input type="submit" name='subEtu' value="Envoyer">
     </form>
     <p>vous avez pas encore inscrire? <a href="register.php">inscription</a></p>
    <?php endif ?>

    <?php if(isset($_POST['admin'])):?>
     <form action="" method="post">
      <label for="ap">Entrer votre identifiant: </label>
      <input type="text" name="id" id="ap" placeholder='username'><br>
      <label for="pw">Entrer votre Mot de passe: </label>
      <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
      <input type="submit" name='subAdm' value="Envoyer">
     </form>
    <?php endif ?>
   </div>
  </center>
  
 
</body>
</html>