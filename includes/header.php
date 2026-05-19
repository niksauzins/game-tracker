<?php
require_once __DIR__ . '/../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GameTracker' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <nav class="flex justify-between p-4">
        <div>
            <a href="/pages/dashboard.php">Dashboard</a>
        </div>
        <div>
            <?php if (isLoggedIn()): ?>
                <a href="../actions/logout.php">Logout</a>
            <?php else: ?>
                <a href="/pages/login.php">Login</a>
                <a href="/pages/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>