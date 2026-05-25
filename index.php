<?php require_once './includes/header.php' ?>

<main class="md:flex-1 flex justify-center items-center pt-20 p-4 sm:p-6 md:p-12">
    <div class="max-w-4xl w-full custom-card flex flex-col md:flex-row p-0 overflow-hidden">
        <div class="bg-custom-yellow w-full md:w-1/2 p-6 md:p-8 flex items-center border-b-4 md:border-b-0 md:border-r-4 border-black">
            <h1 class="font-grotesk font-black text-4xl sm:text-5xl md:text-7xl uppercase tracking-tight leading-none text-left">
                <?= __('index_title_1') ?> <br class="hidden md:inline"> <?= __('index_title_2') ?>
            </h1>
        </div>
        <div class="w-full md:w-1/2 p-6 sm:p-8 md:p-12 flex flex-col justify-center gap-4">
            <p class="mb-2 md:mb-4 font-mono leading-relaxed uppercase font-bold border-l-4 border-custom-red pl-4 text-sm sm:text-base md:text-lg">
                <?= __('index_question') ?>
                <br><br>
                <span class="text-gray-600 normal-case font-normal text-xs sm:text-sm md:text-base">
                    <?= __('index_answer') ?>
                </span>
            </p>
            <a href="pages/register.php" class="custom-btn bg-custom-teal text-sm md:text-lg w-full"><?= __('index_get_started') ?></a>
            <a href="pages/login.php" class="custom-btn text-sm md:text-lg w-full"><?= __('nav_login') ?></a>
        </div>

    </div>
</main>

<?php require_once './includes/footer.php' ?>