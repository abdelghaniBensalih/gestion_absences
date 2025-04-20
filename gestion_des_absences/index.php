<?php
$title = "Accueil";
require_once "includes/header.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
.btns1  {
    display: flex;
    border: solid 4px #5da;
    position: relative;
    width: 500px;
    height: 50px;
    border-radius: 10px;
    margin-top: 60px;
    padding: 10px;
    background-color: rgb(23, 211, 224);
    justify-content: center;
    align-items: center;
}
.btns1>form{
    display: flex;
    flex-direction: row;
    width: 100%;
    justify-content: space-around;
}
.btns1>form>input {
    background-color: rgba(60, 110, 267);
    width: 120px;
    height: 50px;
    margin: 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}
.btns1>form>input:active{
    background-color: rgba(240, 241, 267);
    width: 120px;
    height: 50px;
    margin: 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}
.etudiantConnecte{
    border: solid 2px #0df;
    position: relative;
    width: 350px;
    height: auto;
    border-radius: 15px;
    margin-top: 60px;  
    padding: 10px; 
    line-height: 42px;  
    background-color: rgba(240, 241, 267);    
    box-shadow: 40px 60px 60px #d3d3d3;
}
.adminConnecte{
    border: solid 2px #0df;
    position: relative;
    width: 300px;
    height: auto;
    border-radius: 15px;
    margin-top: 60px;  
    padding: 10px; 
    line-height: 42px;  
    background-color: rgba(241, 241, 267);
    box-shadow: 40px 60px 60px #d3d3d3;
}
.adminConnecte>form>input{
    text-indent: 5px;
    background-color: white;
    height: 26px;
    width: 150px;
    border: none;
    margin: 0px;
    padding: 0px;
    border-radius: 7px;

    
}
.adminConnecte>form>input:focus{
    text-indent: 5px;
    height: 26px;
    width: 150px;
    margin: 0px;
    padding: 0px;
    border: 2px solid black;
    background-color: rgba(245,231,244);
    border-radius: 7px;
    box-sizing: border-box;
}
.etudiantConnecte>form>input{
    text-indent: 5px;
    height: 26px;
    width: 150px;
    border: 1px solid #0df;
    outline: none;
    border-radius: 7px;
}

    </style>
</head>

<body>
    <center>

        <div>
            <div class="btns1">
                <form action="" method="post">
                    <input type="submit" name='etudiant' value="Etudiant(e)">

                    <input type="submit" name='admin' value="Admin">
                </form>
            </div>

            <?php if (isset($_POST['etudiant'])): ?>
                <div class="etudiantConnecte">
                    <form action="" method="post">
                        <label for="ap">Entrer votre APOGEE: </label><br>
                        <input type="text" name="apogee" id="ap" placeholder='username'><br>
                        <label for="pw">Entrer votre Mot de passe: </label><br>
                        <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
                        <input type="submit" id="btn"  name='subEtu' value="Envoyer">
                    </form>
                    <p>vous avez pas encore inscrire? <a href="register.php">inscription</a></p>
                </div>
            <?php endif ?>

            <?php if (isset($_POST['admin'])): ?>
                <div class="adminConnecte">
                    <form action="" method="post">
                        <label for="ap">Entrer votre identifiant: </label><br>
                        <input type="text" name="id" id="ap" placeholder='username'><br>
                        <label for="pw">Entrer votre Mot de passe: </label><br>
                        <input type="password" name="password" id="pw" placeholder='mot de passe'><br>
                        <input type="submit" name='subAdm' id="btn" value="Envoyer">
                    </form>
                </div>
            <?php endif ?>
        </div>
    </center>


</body>

</html>