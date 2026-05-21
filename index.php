<?php require_once './includes/header.php' ?>

<main class="flex-1 flex justify-center items-center p-6 md:p-12">
    <div class="max-w-4xl w-full custom-card flex">
        <div class="bg-custom-yellow md:w-1/2 p-8 flex items-center border-r-4 border-black">
            <h1 class="font-grotesk font-black text-7xl uppercase pb-4 mb-6 text-left">Game <br> Tracker</h1>
        </div>
        <div class="md:w-1/2 p-12 flex flex-col gap-4">
            <p class="mb-4 font-mono leading-relaxed uppercase font-bold border-l-4 border-custom-red pl-4 text-lg">Do you forget what games you want to play? <br><br> <span class="text-gray-600">This tool will help you track your game progress and help you keep on track.</span></p>
            <a href="pages/register.php" class="custom-btn bg-custom-teal text-lg">Get Started</a>
            <a href="pages/login.php" class="custom-btn text-lg">Login</a>
        </div>
    </div>
</main>

<?php require_once './includes/footer.php' ?>