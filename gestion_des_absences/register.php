<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Inscription</title>
</head>
<body>
 <center><div>
  <h1>Bienvenue 😍</h1>
<form action="" method="post">
      <label for="nom">Nom </label>
      <input type="text" name="nom" id="nom" required ><br>

      <label for="prenom">Prenom </label>
      <input type="text" name="prenom" id="prenom" required ><br>

      <label for="apogee">Numero Apogee</label>
      <input type="number" name="apogee" id="apogee" required ><br>
      
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required ><br>

      <label for="password">Mot de Passe</label>
      <input type="password" name="password" id="password" required ><br>
      

      <label for="filiere">Filiere </label>
      <select name="filiere" id="filiere">
       <option value="">------</option>
       <?php foreach($dd as $ligne){?>
        <option value="">$ligne['nom_filiere']</option>
      <?php }?>
      </select>
      <input type="submit" name='subEtu' value="Envoyer">
   </form>
   </div>
   <p>vous avez déjà inscrire? <a href="index.php">se connecte</a></p>
  </center>
</body>
</html>