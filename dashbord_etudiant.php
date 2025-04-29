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
    <h1>Welcome Mr.</h1>
</body>
</html>