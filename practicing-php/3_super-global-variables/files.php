<?php

    require_once "./Shipfast.php";
    $shipfast = new Shipfast();

    // declare(strict_types=1);
    function fetch_post_data(string $a=htmlspecialchars(trim($_POST[$inputName])) ?? null;
        return $post_data;inputName){
        $post_dat
    }

    $errors=[];
    [$username, $email, $age, $password, $upload_url] = "";

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $username = fetch_post_data('username');
        $email = fetch_post_data('email');
        $age = fetch_post_data('age');
        $password = fetch_post_data('pass');

        require_once "./uploads.php";
        // $response = uploadFile($_FILES['avater'], ['jpg','jpeg','png']);
        $response = $shipfast->uploadFile($_FILES['avater'], ['jpg','jpeg','png'], 10);
        if($response['ok']){
            $upload_url = $response['path'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>

    <?php if ($_SERVER['REQUEST_METHOD']=='POST'){ ?>
        <img src="<?= $upload_url ?>" alt="Avater image">
        <h2>Username: <?= $username ?></h2>
        <h2>Email: <?= $email ?></h2>
        <h2>Age: <?= $age ?></h2>
        <h2>Password: <?= "***** Not for your eyes!" ?></h2>
    <?php } ?>

    <form action="" enctype="multipart/form-data" method="post">
        <input type="text" name="username" placeholder="What's your username" required>
        <br><br>
        <input type="email" name="email" placeholder="What's your email" required>
        <br><br>
        <input type="number" name="age" placeholder="What's your age" required>
        <br><br>
        <input type="password" name="pass" placeholder="What's your pass" required>
        <br><br>
        <img id="preview" style="display:hidden" alt="Avater preview">
        <br><br>
        
        <?= $shipfast->uploadField('preview','image','','',['name'=>'avater']) ?>
        <br><br>
        <button>Hook me up</button>
    </form>
</body>
</html>