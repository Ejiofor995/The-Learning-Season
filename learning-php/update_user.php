<?php 

    [
        $error, 
        $message, 
        $user_id, 
        $username, 
        $email,
        $password,
        $confirm_password
    ] = '';

    if ((isset($_GET['success']) && isset($_GET['message']) && $_GET['success'])==1){
        $error=false;
        $message = $_GET['message'];
    } elseif ((isset($_GET['success']) && isset($_GET['message']) && $_GET['success'])==0){
        $error = true;
        $message = $_GET['message'] ?? '';
    }

    

    function sanitize_post_data($data){
        $sanitized_data = htmlspecialchars(trim($_POST[$data]));
        return $sanitized_data;
    }

    function confirm_password($pass1, $pass2){
        if ($pass1 === $pass2){
            return true;
        }else{
            return false;
        }
    }



    if ($_SERVER['REQUEST_METHOD']=='POST') {

        $user_id = sanitize_post_data('user_id');
        $username = sanitize_post_data('username');
        $email = sanitize_post_data('email');
        $password = sanitize_post_data('password');
        $confirm_password = sanitize_post_data('confirm_password');

        $password_match = confirm_password($password, $confirm_password);
        if ($password_match){
            $password = password_hash($password, PASSWORD_DEFAULT);
            try{
            require_once './includes/db_connection.inc.php';

            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, pass=? WHERE id=?');
            $stmt->execute([$username, $email, $password, $user_id]);

            [$error, $user_id, $username, $email, $password, $confirm_password] = '';

            header("Location: /learning-php/update_user.php?success=1&message=User updated successfully!");
            } catch (PDOException $err){
                header("Location: /learning-php/update_user.php?success=0?message='Something went wrong: '.{$err->getMessage()}");
            }
        }else{
            $error=true;
            $message = 'Both passwords did\'t match!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en" data-theme="luxury">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>

    <script src="./frontend/libraries/tailwind.js"></script>
    <script src="./frontend/libraries/alpine.js" defer></script>
    <link rel="stylesheet" href="./frontend/libraries/daisyui.css">
    <link rel="stylesheet" href="./frontend/libraries/daisyui-themes.css">

</head>
<body>
    <h2 class="text-2xl font-bold text-primary py-8 border-b-2 text-center">Welcome Dark Zuckerberg, Let's Update a new user using this algorithm you built.</h2>

    <div class="container h-screen w-full flex justify-center items-center">
        <form class="flex flex-col gap-4 justify-center items-center w-[80%] md:w-1/2 m-auto inset-0 p-4 shadow rounded-md shadow-primary shadow-white" action="" method="post">
            <h2 class="text-lg font-semibold text-primary">Update a user</h2>

            <!-- PHP conditionsal error rendering -->
            <?php if ($error) { ?>
                <span class="'text-md text-error"><?= $message ?></span>
            <?php } elseif (isset($_GET['success']) && $_GET['success']==1 && !$error) { ?>
                <span class="'text-md text-success"><?= $_GET['message'] ?? '' ?></span>
            <?php } ?>

            <input required type="number" value="<?= $user_id ?>" name="user_id" class="input input-primary w-full" placeholder="Enter user's id">

            <input required type="text" value="<?= $username ?>" name="username" class="input input-primary w-full" placeholder="Enter new username">

            <input required type="email" value="<?= $email ?>" name="email" class="input input-primary w-full" placeholder="Enter new email">

            <input required type="password" name="password" class="input input-primary w-full" placeholder="Enter new password">

            <input required type="password" name="confirm_password" class="input input-primary w-full" placeholder="Enter new password again">

            <button class="btn btn-primary w-full">Create User</button>
        </form>
    </div>
</body>
</html>