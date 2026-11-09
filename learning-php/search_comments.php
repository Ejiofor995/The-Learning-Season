<?php 
    $username='';
    $error = '';
    $comments = '';

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $username = htmlspecialchars(trim($_POST['username']));

        try{
            require_once './includes/db_connection.inc.php';
            $stmt=$pdo->prepare('SELECT users.username, users.email, comments.content, comments.created_at FROM users INNER JOIN comments ON users.id=comments.users_id where users.username LIKE ?');
            $stmt->execute(["%{$username}%"]);
            $comments=$stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $err){
            $error='Something went wrong: '.$err->getMessage();
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
    <h2 class="text-2xl font-bold text-primary py-8 border-b-2 text-center">Welcome Dark Zuckerberg, Let's search for comments using this algorithm you built.</h2>

    <div class="mt-8 w-full">
        <form class="flex flex-col gap-4 justify-center items-center w-[80%] md:w-1/2 m-auto inset-0 p-4 shadow rounded-md shadow-primary shadow-white mx-auto" action="" method="post">
            <h2 class="text-lg font-semibold text-primary">Search comment by user</h2>

            <input required type="text" value="<?= $username ?>" name="username" class="input input-primary w-full" placeholder="Enter user's name">

            <button class="btn btn-primary w-full">Search</button>
        </form>
    </div>

    <?php if (empty($comments)) {
        echo "<h2 class='text-2xl font-semibold w-full text-center my-8'>No comment matches your search.</h2>";
    } ?>

    <?php if ($error) {
        echo "<h2 class='text-2xl font-semibold w-full text-center my-8'>{$error}</h2>";
    } ?>
    


<div class="overflow-x-auto w-full">
    <table class="table table-zebra table-bordered w-full mt-8">
        <thead class="bg-base-200">
            <tr>
                <?php
                    if (!empty($comments)) {
                        foreach ($comments[0] as $k => $v) {
                            switch($k){
                                case('username'):
                                    $k='USERNAME';
                                    break;

                                case('email'):
                                    $k='EMAIL';
                                    break;
                                
                                case('content'):
                                    $k='COMMENT';
                                    break;
                                
                                case('created_at'):
                                    $k='DATE/TIME';
                                    break;
                                
                                default:
                                    $k='____';

                            }
                            echo '<th>' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '</th>';
                        }
                    }
                ?>
            </tr>
        </thead>

        <tbody>
            <?php
                if (!empty($comments)) {
                    foreach ($comments as $comment) {
                        echo '<tr>';

                        foreach ($comment as $v) {
                            echo '<td>' . htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8') . '</td>';
                        }

                        echo '</tr>';
                    }
                }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>