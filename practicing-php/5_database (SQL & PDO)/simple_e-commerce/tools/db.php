<?php
$dsn = 'mysql:host=localhost;dbname=practicing-php;charset=utf8mb4';
$username = 'root';
$password = '';

try{
    $pdo = new PDO($dsn,$username,$password,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    // echo "Database connected successully!";
}catch(PDOException $err){
    error_log("Failed to connect the database: ".$err->getMessage());
    http_response_code(500);
     exit('DataBase unavailable, please try again later!');
}