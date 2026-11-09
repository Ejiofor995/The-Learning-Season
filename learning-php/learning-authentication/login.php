<?php
    require_once "./tools/toolkit.php";
    require_once "./tools/db.php";
    
    session_start();

    if (isset($_SESSION['email'])){
        header('Location: ./index.php');
    }

    [$email,$password,$error]='';

    if ($_SERVER['REQUEST_METHOD']=='POST'){
        $email = sanitize_post_data('email');
        $password = sanitize_post_data('password');

        try{
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?? '';

        // Verify password
        $password_check = password_verify($password, $user['pass']);

        if ($password_check){
            $_SESSION['id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['email']=$user['email'];
            $_SESSION['created_at']=$user['created_at'];

            header('Location: ./index.php'); exit;
        }
        
        }catch(PDOException $err){
            $error='Something went wrong: '.$err->getMessage();
        }

        }else{
            $error = "Invalid Credentials";
        }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back in the Corner | Login</title>
    <script src="../frontend/libraries/tailwind.js"></script>
    <link rel="stylesheet" href="../frontend/libraries/daisyui.css">
    <style>:root{--paper:#f2f0e9;--ink:#252724;--red:#b43d2e;--gold:#e5b354}body{background:var(--paper);color:var(--ink)}</style>
</head>
<body>
    <?php require_once "./components/navbar.php" ?>

    <main class="grid min-h-[calc(100vh-4rem)] place-items-center px-4 py-8">
        <section class="grid w-full max-w-4xl overflow-hidden rounded-lg bg-white shadow-xl md:grid-cols-2">
            <div class="flex flex-col justify-between gap-8 bg-[#252724] p-8 text-white sm:p-10">
                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-[#e5b354]">Round 02 · Welcome back</p>
                    <h1 class="text-4xl font-black sm:text-5xl">Back in<br>your corner.</h1>
                    <p class="mt-4 text-sm leading-6 text-white/75">Pick up where you left off. Every round counts.</p>
                </div>
                <img class="aspect-[16/9] w-full rounded-md object-cover" src="../frontend/assets/ippo.jpg" alt="Ippo ready for another round">
            </div>

            <form class="p-8 sm:p-10" action="" method="post">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#b43d2e]">Login · Round 02</p>
                <h2 class="mt-2 text-2xl font-bold">Welcome back, champ.</h2>
                <p class="mb-6 mt-1 text-sm text-base-content/65">Your corner saved you a spot.</p>

                <p class="mb-6 mt-1 text-sm text-error"><?= $error ?></p>

                <fieldset class="fieldset mb-4">
                    <legend class="fieldset-legend">Email</legend>
                    <input class="input input-bordered h-12 w-full bg-white text-[#252724]" type="email" placeholder="you@example.com" name="email" value="<?= $email ?>" >
                </fieldset>
                <fieldset class="fieldset mb-4">
                    <legend class="fieldset-legend">Password</legend>
                    <input class="input input-bordered h-12 w-full bg-white text-[#252724]" type="password" placeholder="Your password" name="password">
                </fieldset>
                <label class="label mb-5 cursor-pointer justify-start gap-2">
                    <input class="checkbox checkbox-sm" type="checkbox">
                    <span>Keep me in your corner</span>
                </label>
                <button class="btn h-12 w-full border-0 bg-[#b43d2e] text-base font-bold text-white hover:bg-[#963124]" type="submit">Back to training</button>
                <p class="mt-5 text-center text-sm">New to the gym? <a class="font-bold text-[#b43d2e] underline" href="./register.php">Join the corner</a></p>
            </form>
        </section>
    </main>
</body>
</html>
