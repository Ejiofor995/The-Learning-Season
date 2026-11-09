<?php
session_start();

require_once "./Shipfast.php";
$shipfast = new Shipfast();

// if(isset($_SESSION['email'])) header('Location: ./index.php');

// if(!$_SESSION['csrf']) $_SESSION['csrf']=bin2hex(random_bytes(32));
$shipfast->csrf('set');

$errors=$formData=[];

function display($output){
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD']==='POST'){
    // if(!hash_equals($_SESSION['csrf'],$_POST['csrf'])) $errors['csrf']='csrf verification failed!';
    $csrf = $shipfast->csrf('verify',$_POST['csrf']);

    $formData=[
        'email'=>trim($_POST['email']),
        'pass'=>password_hash(trim($_POST['pass']), PASSWORD_DEFAULT),
    ];

    // if(mb_strlen($formData['pass'])<5 || mb_strlen($formData['pass'])>20) $errors['pass']='pass must be within 5 to 20 characters';

    if(!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors['email']='invalid email!';

    if(!$errors){
        $_SESSION['email']=$formData['email'];
        header('Location: ./index.php'); exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

</head>
<body>

    <?php require_once "./tools/navbar.php" ?>

    <?= $_SESSION['csrf']??'No csrf token is present' ?>

    <form action="" method="post">
        <h2>Login Form</h2>
        <span class="text-lg text-error" style="color:red"><?= display($errors['csrf']??'') ?></span>

        <br><br>
        <hr>
        <br>
        <?= $shipfast->csrf('field')['html'] ?>
        <label for="pass">Email</label>
        <br>
        <input type="text" name="email">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['email']??'') ?></span>

        <br><br>

        <label for="pass">Password</label>
        <br>
        <input type="password" name="pass">
        <br>
        <span class="text-lg text-error" style="color:red"><?= display($errors['pass']??'') ?></span>

        <br><br>

        <button>Login</button>


    </form>
</body>
</html>