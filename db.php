<?php
// db.php — connexion PDO à la base Boukla
$pdo = new PDO('mysql:host=localhost;dbname=boukla;charset=utf8', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
