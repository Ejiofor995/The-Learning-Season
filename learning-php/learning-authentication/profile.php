<?php
session_start();

if (empty($_SESSION['email'])){
    header('Location: ./login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Corner | Profile</title>
    <script src="../frontend/libraries/tailwind.js"></script>
    <link rel="stylesheet" href="../frontend/libraries/daisyui.css">
    <style>:root{--paper:#f2f0e9;--ink:#252724;--red:#b43d2e;--gold:#e5b354}body{background:var(--paper);color:var(--ink)}</style>
</head>
<body>
    <?php require_once "./components/navbar.php" ?>

    <main class="grid min-h-[calc(100vh-4rem)] place-items-center px-4 py-8">
        <section class="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-xl">
            <header class="flex items-center gap-5 bg-[#252724] p-6 text-white sm:p-8">
                <div class="grid size-16 shrink-0 place-items-center rounded-full bg-[#e5b354] text-xl font-black text-[#252724]"><?= $_SESSION['username'][0].$_SESSION['username'][1].$_SESSION['username'][2].$_SESSION['username'][3] ?></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#e5b354]">Your corner · Round 01</p>
                    <h1 class="mt-1 text-2xl font-black sm:text-3xl"><?= $_SESSION['username'] ?></h1>
                    <p class="text-sm text-white/70"><?= $_SESSION['email'] ?></p>
                </div>
            </header>

            <div class="p-6 sm:p-8">
                <h2 class="mb-5 text-lg font-bold">Fighter details</h2>
                <dl class="divide-y divide-black/10">
                    <div class="flex justify-between gap-4 py-4">
                        <dt class="text-sm text-base-content/60">Training since</dt>
                        <dd class="text-right text-sm font-semibold"><?= $_SESSION['created_at'] ?></dd>
                    </div>
                    <div class="flex justify-between gap-4 py-4">
                        <dt class="text-sm text-base-content/60">Specialty</dt>
                        <dd class="text-right text-sm font-semibold">API Development</dd>
                    </div>
                    <div class="flex justify-between gap-4 py-4">
                        <dt class="text-sm text-base-content/60">Other skills</dt>
                        <dd class="text-right text-sm font-semibold text-[#b43d2e]">Trading and Machine Learning!</dd>
                    </div>
                </dl>
                <a class="btn mt-5 border-0 bg-[#b43d2e] text-white hover:bg-[#963124]" href="./index.php">Back to the ring</a>
            </div>
        </section>
    </main>
</body>
</html>
