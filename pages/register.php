<?php
session_start();
require_once '../config/db.php';
// If user already has a session, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Register';
?>

<?php require_once '../includes/header.php' ?>

<main class="md:flex-1 flex justify-center items-center p-6 pt-14">
    <div class="max-w-md w-full custom-card !p-8">
        <h1 class="font-grotesk font-black text-4xl uppercase border-b-4 border-black pb-4 mb-6 text-center">Register</h1>

        <form action="../actions/register.php" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm">Username</label>
                <input type="text" name="username" placeholder="Choose a name" required class="custom-input">
            </div>

            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm">Email</label>
                <input type="email" name="email" placeholder="example@email.com" required class="custom-input">
            </div>

            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm">Password</label>
                <input type="password" name="password" placeholder="********" required class="custom-input mt-2">
            </div>

            <?php renderFlash('-mt-2') ?>

            <button type="submit" class="custom-btn bg-custom-yellow text-lg ">Create account</button>
        </form>

        <p class="font-mono mt-6 font-bold text-center uppercase text-sm">Already registered? <a href="login.php" class="text-custom-red hover:underline"> Login Here</a></p>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>