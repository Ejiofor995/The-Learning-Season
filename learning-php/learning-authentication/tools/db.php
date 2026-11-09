<?php

$dsn="mysql:host=localhost;dbname=learning-php-authenticarion;";
$dbusername="root";
$dbpassword="";

$db_error='';

try{
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $err){
    $db_error = "Database connection failed: ".$err->getMessage();
}