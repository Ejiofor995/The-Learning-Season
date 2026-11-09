<?php 
    $user_id='';
    $error = '';
    $success='';
    $comments = '';

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $user_id = htmlspecialchars(trim($_POST['user_id']));

        try{
            require_once './includes/db_connection.inc.php';
            $pdo->beginTransaction();
            $stmt=$pdo->prepare('DELETE FROM comments WHERE users_id = ?');
            $stmt->execute([$user_id]);
            $stmt=$pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $pdo->commit();
            $success='User has been deleted successfully!';
            $error='';
        } catch (PDOException $err){
            if ($pdo->inTransaction()){
                $pdo->rollBack();
            }
            $error='Something went wrong: '.$err->getMessage();
            $success='';
        }

    }
?>

<!DOCTYPE html>
<html lang="en" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search comment</title>

    <script src="./frontend/libraries/tailwind.js"></script>
    <script src="./frontend/libraries/alpine.js" defer></script>
    <link rel="stylesheet" href="./frontend/libraries/daisyui.css">
    <link rel="stylesheet" href="./frontend/libraries/daisyui-themes.css">

</head>
<body>
    <h2 class="text-2xl font-bold text-primary py-8 border-b-2 text-center">Welcome Dark Zuckerberg, Let's delete a user using this algorithm you built.</h2>

    <div class="mt-8 w-full">
        <form class="flex flex-col gap-4 justify-center items-center w-[80%] md:w-1/2 m-auto inset-0 p-4 shadow rounded-md shadow-primary shadow-white mx-auto" action="" method="post">
            <h2 class="text-lg font-semibold text-primary">Delete user by id</h2>
                <?php if ($error) {
                    echo "<h2 class='text-md font-semibold w-full text-error text-center my-8'>{$error}</h2>";
                } ?>

                <?php if ($success) {
                    echo "<h2 class='text-md font-semibold w-full text-success text-center my-8'>{$success}</h2>";
                } ?>

            <input required type="number" value="<?= $user_id ?>" name="user_id" class="input input-primary w-full" placeholder="Enter user's id">

            <button class="btn btn-primary w-full">Delete User</button>
        </form>
    </div>
    

</body>
</html>