<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// If user is logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('/pages/dashboard.php');
}

$pageTitle = __('nav_login') . ' | ' . __('app_title');

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<?php require_once BASE_PATH . '/includes/header.php' ?>

<main class="md:flex-1 flex justify-center items-center p-6 pt-20">
    <div class="max-w-md w-full custom-card !p-8">
        <h1 class="font-grotesk font-black text-4xl uppercase border-b-4 border-black pb-4 mb-6 text-center"><?= __('nav_login') ?></h1>

        <form action="<?= BASE_URL ?>/actions/login.php" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm"><?= __('email') ?></label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="<?= __('enter_email') ?>" required class="custom-input">
            </div>
            <div class="flex flex-col gap-2">
                <label class="uppercase font-mono font-bold text-sm"><?= __('password') ?></label>
                <input type="password" name="password" placeholder="********" required class="custom-input mt-2">
            </div>

            <?php renderFlash('-mt-2') ?>

            <button type="submit" class="custom-btn bg-custom-yellow text-lg"><?= __('nav_login') ?></button>
        </form>

        <p class="font-mono mt-6 font-bold text-center uppercase text-sm"><?= __('no_account') ?> <a href="<?= BASE_URL ?>/pages/register.php" class="text-custom-red hover:underline"> <?= __('register_now') ?></a></p>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/footer.php' ?>