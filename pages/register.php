<?php
session_start();
require_once '../config/db.php';
// If user already has a session, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
$pageTitle = 'Register';
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-center">
    <div class="max-w-xs w-full">
        <h1>Registration Form</h1>

        <form action="../actions/register.php" method="POST" class="flex flex-col gap-2">
            <input type="text" name="username" placeholder="Username" required class="border">
            <input type="email" name="email" placeholder="example@email.com" required class="border">
            <input type="password" name="password" placeholder="********" required class="border">

            <?php if ($error): ?>
                <p class="bg-red-50 text-red-700 p-1 rounded"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="bg-blue-600 text-white">Register</button>
        </form>

        <p>Already registered? <a href="login.php">Login</a></p>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>