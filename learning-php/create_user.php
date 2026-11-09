<?php 
    $error=false;
    $message='';

    $username='';
    $email='';

    // We display success message after creating user successfully
    if (isset($_GET['success']) && $_GET['success']==1){
        $error = false;
        $message = 'User created successfully';
    }else{
        $_GET['success']=0;
    }

    function sanitize_post_data($data){
        $sanitized_data = htmlspecialchars(trim($_POST[$data]) ?? '');
        return $sanitized_data;
    }

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $username=sanitize_post_data('username');
        $email=sanitize_post_data('email');
        $password=sanitize_post_data('password');
        $confirm_password=sanitize_post_data('confirm_password');
        
        if (!($password==$confirm_password)){
            $error=true;
            $message='Both passwords don\'t match!';
        } else {
            try{
                require_once "./includes/db_connection.inc.php";
                $stmt = $pdo->prepare('INSERT INTO users (username, email, pass) VALUES (?,?,?)');
                $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                [$username,$password,$email]='';
                
                // Redirect User to avoid creating extra rows by refreshing
                header('Location: /learning-php/create_user.php?success=1');
            } catch (PDOException $error){
                $message = "Something went wrong: ".$error->getMessage();
            }
        }
    }
?>



<!DOCTYPE html>
<html lang="en" data-theme="luxury">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>

    <script src="./frontend/libraries/tailwind.js"></script>
    <script src="./frontend/libraries/alpine.js" defer></script>
    <link rel="stylesheet" href="./frontend/libraries/daisyui.css">
    <link rel="stylesheet" href="./frontend/libraries/daisyui-themes.css">

</head>
<body>
    <h2 class="text-2xl font-bold text-primary py-8 border-b-2 text-center">Welcome Dark Zuckerberg, Let's create a new user using this algorithm you built.</h2>

    <div class="container h-screen w-full flex justify-center items-center">
        <form class="flex flex-col gap-4 justify-center items-center w-[80%] md:w-1/2 m-auto inset-0 p-4 shadow-md rounded-md shadow-primary shadow-white" action="" method="post">
            <h2 class="text-lg font-semibold text-primary">Create new user</h2>

            <!-- PHP conditionsal error rendering -->
            <?php if ($error) { ?>
                <span class="'text-md text-error"><?= $message ?></span>
            <?php } elseif (isset($_GET['success']) && $_GET['success']==1) { ?>
                <span class="'text-md text-success"><?= $message ?></span>
            <?php } ?>

            <input required type="text" value="<?= $username ?>" name="username" class="input input-primary w-full" placeholder="Enter username">
            <input required type="email" value="<?= $email ?>" name="email" class="input input-primary w-full" placeholder="Enter email">
            <input required type="password" name="password" class="input input-primary w-full" placeholder="Enter password">
            <input required type="password" name="confirm_password" class="input input-primary w-full" placeholder="Enter password again">
            <button class="btn btn-primary w-full">Create User</button>
        </form>
    </div>
</body>
</html>