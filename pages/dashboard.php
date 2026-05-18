<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Dashboard';
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-center">
    <h1>Hello, <?= htmlspecialchars($_SESSION['username']) ?></h1>
</main>

<?php require_once '../includes/footer.php' ?>