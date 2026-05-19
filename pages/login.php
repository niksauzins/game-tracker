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

<main class="min-h-screen flex justify-center items-center">
    <div class="max-w-xs w-full">
        <h1>Login Form</h1>

        <form action="../actions/login.php" method="POST" class="flex flex-col gap-2">
            <input type="email" name="email" placeholder="example@email.com" required class="border">
            <input type="password" name="password" placeholder="********" required class="border">

            <?php if ($error): ?>
                <p class="bg-red-50 text-red-700 p-1 rounded"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="bg-blue-600 text-white">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>