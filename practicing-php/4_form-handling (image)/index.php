<?php 
session_start(); 

function display($output) {return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
</head>
<body>
    <?php require_once "./tools/navbar.php" ?>

    <img src="<?= display($_SESSION['avater']??'') ?>">

    <h2>
        <ol>
            <li>Username: <?= display($_SESSION['username'] ?? 'Guest') ?> </li>
            <li>Email: <?= display($_SESSION['email'] ?? 'example email') ?> </li> 
            <li>Password: <?= display($_SESSION['pass'] ?? '********') ?> </li> 
        </ol>
    </h2>
</body>
</html>