<?php
session_start();
require_once '../config/db.php';
// If user is logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
$pageTitle = 'Login';
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 flex justify-center items-center p-6">
    <div class="max-w-md w-full custom-card !p-8">
        <h1 class="font-grotesk font-black text-4xl uppercase border-b-4 border-black pb-4 mb-6 text-center">Login</h1>

        <form action="../actions/login.php" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm">Email</label>
                <input type="email" name="email" placeholder="Enter email" required class="custom-input">
            </div>
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm">Password</label>
                <input type="password" name="password" placeholder="********" required class="custom-input">
            </div>

            <button type="submit" class="custom-btn bg-custom-yellow mt-2 text-lg">Login</button>
        </form>

        <p class="font-mono mt-6 font-bold text-center uppercase text-sm">No account yet? <a href="register.php" class="text-custom-red hover:underline"> Register now</a></p>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>