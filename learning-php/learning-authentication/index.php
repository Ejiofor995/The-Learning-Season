<?php 
    session_start();
    $authenticated=null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn PHP | The Next Round</title>

    <script src="../frontend/libraries/tailwind.js"></script>
    <link rel="stylesheet" href="../frontend/libraries/daisyui.css">
    <style>
        :root {
            --corner-paper: #f2f0e9;
            --corner-ink: #252724;
            --corner-red: #b43d2e;
            --corner-gold: #e5b354;
        }

        body { background: var(--corner-paper); color: var(--corner-ink); }
    </style>
</head>
<body>

    <?php require_once "./components/navbar.php"; ?>

    <main class="grid min-h-[calc(100vh-4rem)] place-items-center px-4 py-8 sm:px-6">
        <section class="grid w-full max-w-5xl overflow-hidden rounded-lg bg-white shadow-xl md:grid-cols-2">
            <div class="flex flex-col items-start justify-center bg-[#252724] p-8 text-white sm:p-12">
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.2em] text-[#e5b354]">Your next round starts here</p>
                <h1 class="text-5xl font-black leading-tight sm:text-6xl">Learn PHP.<br>Go the distance.</h1>
                <p class="my-5 max-w-sm text-sm leading-6 text-white/75">The goal is to learn, not to win or lose. Show up, keep going, and take it one round at a time.</p>
                <?php 
                    if ($authenticated){
                        echo '
                        <a class="btn border-0 bg-[#b43d2e] text-white hover:bg-[#963124]" href="./profile.php">Check out your Profile!</a>
                        ';
                    }else{
                        echo '
                        <a class="btn border-0 bg-[#b43d2e] text-white hover:bg-[#963124]" href="./register.php">Start training</a>
                        ';
                    }
                ?>
            </div>
            <img class="h-full min-h-72 w-full object-cover object-top md:min-h-[32rem]" src="../frontend/assets/ippo.jpg" alt="Ippo ready for another round">
        </section>
    </main>
    
    <?php require_once "./components/footer.php"; ?>
</body>
</html>