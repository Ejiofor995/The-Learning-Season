<?php
// Setting up database credentials (host, dbname, dbusername and dbpassword)
$dsn="mysql:host=localhost;dbname=learningphp;";
$dbusername="root";
$dbpassword="";

// Attempting the database connection
try {
    $pdo=new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Successfully connected to database! " . random_int(1,5);
} catch (PDOEXCEPTION $connection_error) {
    echo "Failed to connect to database: {$connection_error->getMessage()}";
}