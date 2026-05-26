<?php
session_start();
require_once '../config/db.php';

// If user already has a session, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$pageTitle = __('nav_register') . ' | ' . __('app_title');

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<?php require_once '../includes/header.php' ?>

<main class="md:flex-1 flex justify-center items-center p-6 pt-14">
    <div class="max-w-md w-full custom-card !p-8">
        <h1 class="font-grotesk font-black text-4xl uppercase border-b-4 border-black pb-4 mb-6 text-center"><?= __('nav_register') ?></h1>

        <form action="../actions/register.php" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm"><?= __('username') ?></label>
                <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" placeholder="<?= __('choose_name') ?>" required class="custom-input">
            </div>

            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm"><?= __('email') ?></label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="<?= __('email_placeholder') ?>" required class="custom-input">
            </div>

            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm"><?= __('password') ?></label>
                <input type="password" name="password" placeholder="********" required class="custom-input mt-2">
            </div>

            <?php renderFlash('-mt-2') ?>

            <button type="submit" class="custom-btn bg-custom-yellow text-lg "><?= __('create_account') ?></button>
        </form>

        <p class="font-mono mt-6 font-bold text-center uppercase text-sm"><?= __('already_registered') ?> <a href="login.php" class="text-custom-red hover:underline"> <?= __('login_here') ?></a></p>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>