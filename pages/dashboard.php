<?php
session_start();
require_once '../config/db.php';

requireLogin();

$pageTitle = 'Dashboard';
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-center">
    <h1>Hello, <?= htmlspecialchars($_SESSION['username']) ?></h1>
</main>

<?php require_once '../includes/footer.php' ?>