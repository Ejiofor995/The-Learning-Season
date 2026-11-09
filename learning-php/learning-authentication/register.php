<?php
    require_once "./tools/toolkit.php";
    
    session_start();

    if (isset($_SESSION['email'])){
        header('Location: ./index.php'); exit;
    }

    // Initialize all variables to an empty string at 1st
    [$username, $email, $password, $confirm_password, $error] = '';

    
    // Helper function to get and sanitize post data

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        // Assign each post data to a variable if a post request is sent using our helper function
        $username = sanitize_post_data('username');
        $email = sanitize_post_data('email');
        $password = sanitize_post_data('password');
        $confirm_password = sanitize_post_data('confirm_password');

        if ($password !== $confirm_password){
            $error = "Both password don't add up!";
        } else {
            try{
                require_once "./tools/db.php";

                $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
                $stmt->execute([$email]);
                $email_taken = $stmt->fetch(PDO::FETCH_ASSOC) ?? '';

                $stmt = $pdo->prepare('SELECT id FROM users WHERE username=?');
                $stmt->execute([$username]);
                $username_taken = $stmt->fetch(PDO::FETCH_ASSOC) ?? '';

                if ($email_taken){
                    $error = "This email is already taken by another fighter";
                }elseif ($username_taken){
                    $error = "This username is already taken by another fighter";
                }else{
                    $stmt=$pdo->prepare('INSERT INTO users (username, email, pass) VALUES (?,?,?)');
                    $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    $user_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id=?');
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION['id']=$user['id'];
                    $_SESSION['username']=$user['username'];
                    $_SESSION['email']=$user['email'];
                    $_SESSION['created_at']=$user['created_at'];

                    header('Location: ./index.php'); exit;
                }

            }catch (PDOException){
                $error = $db_error;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Corner | Register</title>

    <script src="../frontend/libraries/tailwind.js"></script>
    <link rel="stylesheet" href="../frontend/libraries/daisyui.css">
    <style>
        :root {
            --corner-paper: #f2f0e9;
            --corner-ink: #252724;
            --corner-red: #b43d2e;
            --corner-gold: #e5b354;
        }

        body {
            background-color: var(--corner-paper);
            color: var(--corner-ink);
        }
    </style>
</head>
<body>
    <?php require_once "./components/navbar.php" ?>

    <main class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4 py-8 sm:px-6">
        <section class="grid w-full max-w-5xl overflow-hidden rounded-lg border border-black/10 bg-white shadow-xl md:grid-cols-2">
            <div class="flex flex-col justify-between gap-8 bg-[#252724] p-6 text-white sm:p-9">
                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-[#e5b354]">Your next round starts here</p>
                    <h1 class="text-4xl font-black leading-tight sm:text-5xl">Get in<br>the ring.</h1>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-white/75">Sign up, champ. The winning can wait; showing up is round one.</p>
                </div>
                <img class="aspect-[16/9] w-full rounded-md object-cover" src="../frontend/assets/ippo.jpg" alt="Ippo ready to box in the ring">
                <p class="border-l-2 border-[#e5b354] pl-3 text-sm font-medium text-white/85">No title required. Just heart, hustle, and a password you can remember.</p>
            </div>

            <div class="p-6 sm:p-9">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#b43d2e]">Registration · Round 01</p>
                <h2 class="mt-2 text-2xl font-bold">Join the corner</h2>
                <p class="mt-1 mb-6 text-sm text-base-content/65">A few details, then you're on the roster.</p>

                <form class="w-full" action="" method="post">
                    <p class="mb-6 mt-1 text-sm text-error"><?= $error ?></p>

                    <fieldset class="fieldset mb-4">
                        <legend class="fieldset-legend">What's your name, champ?</legend>
                        <input type="text" class="input input-bordered h-12 w-full text-white" placeholder="Your name" name="username" value="<?=  $username ?>" />
                        <p class="label">Required. No dodging this round.</p>
                    </fieldset>

                    <fieldset class="fieldset mb-4">
                        <legend class="fieldset-legend">Where do we send your corner notes?</legend>
                        <input type="text" class="input input-bordered h-12 w-full text-white" placeholder="you@example.com" name="email" value="<?=  $email ?>" />
                        <p class="label">Required. Make it an inbox you check.</p>
                    </fieldset>

                    <fieldset class="fieldset mb-4">
                        <legend class="fieldset-legend">Create a password</legend>
                        <input type="password" class="input input-bordered h-12 w-full text-white" placeholder="Make it a tough one" name="password" />
                        <p class="label">Required. Give it a fighting chance.</p>
                    </fieldset>

                    <fieldset class="fieldset mb-6">
                        <legend class="fieldset-legend">One more time, just to be sure?</legend>
                        <input type="password" class="input input-bordered h-12 w-full text-white" placeholder="Same password, no rematch" name="confirm_password" />
                        <p class="label">Required. No password déjà vu.</p>
                    </fieldset>

                    <button class="btn h-12 w-full border-0 bg-[#b43d2e] text-base font-bold text-white hover:bg-[#963124]">Sign me up, coach</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>