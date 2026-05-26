<?php
require_once __DIR__ . '/../config/db.php';
$current_lang = $_SESSION['lang'] ?? 'en';

// Build correct language URL
$newParams = array_merge($_GET, ['lang' => $current_lang === 'en' ? 'lv' : 'en']);
$languageUrl = '?' . http_build_query($newParams);
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? __('app_title') ?></title>
    <link rel="icon" href="<?= BASE_URL ?>/favicon.png" type="image/x-icon">

    <!-- Custom Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;700;900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN setup -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-red': '#ff5f5f',
                        'custom-yellow': '#FFDE4D',
                        'custom-teal': '#58FFDA',
                        'custom-bg': '#FFFDF6',
                    },
                    fontFamily: {
                        grotesk: ['"Space Grotesk"', 'sans-serif'],
                        mono: ['"Space Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer utilities {
            .custom-border { @apply border-4 border-black; }

            .custom-shadow {
                @apply shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200;
            }

            .custom-hover {
                @apply hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)];
            }

            .custom-btn {
                @apply font-grotesk font-black tracking-tight uppercase custom-border text-black px-4 py-2 custom-shadow custom-hover inline-block cursor-pointer text-center;
            }

            .custom-input {
                @apply font-mono custom-border px-4 py-3 bg-white text-black placeholder-gray-500 transition-all duration-200 focus:outline-none focus:-translate-x-1 focus:-translate-y-1 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)];
            }

            .custom-input:focus {
                outline: none !important;
            }

            .custom-card {
                @apply p-5 bg-white custom-border custom-shadow;
            }
        }
    </style>

    <style>
        body {
            background-color: #FFFDF6;
            background-image: radial-gradient(circle, #000000 1px, transparent 1.5px);
            background-size: 24px 24px;
        }
    </style>

    <script>
        // Helper function for setting up modals
        function setupModal(modalId, openModalId, closeModalId) {
            const modal = document.getElementById(modalId);
            const openBtn = document.getElementById(openModalId);
            const closeBtn = document.getElementById(closeModalId);

            const toggle = () => modal.classList.toggle('hidden');

            if (openBtn) openBtn.addEventListener('click', toggle);
            if (closeBtn) closeBtn.addEventListener('click', toggle);

            window.addEventListener('click', (e) => {
                if (e.target === modal) toggle();
            })
        }
    </script>
</head>

<body class="min-h-screen flex flex-col md:flex-row text-black relative">
    <aside class="w-full md:max-w-72 bg-custom-yellow flex flex-col border-b-4 md:border-b-0 md:border-r-4 border-black z-40 md:sticky md:top-0 md:h-screen shrink-0">

        <!-- Mobile navbar -->
        <div class="flex items-center justify-between p-4 md:hidden">
            <h1 class="text-3xl font-black font-grotesk tracking-tight uppercase"><?= __('app_title') ?></h1>
            <button id="mobileMenuBtn" class="custom-btn !px-3 !py-1.5 bg-white text-sm">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Desktop navbar -->
        <div id="navMenuContent" class="hidden md:flex flex-col flex-1">
            <div class="flex-1 p-6 md:p-8 pt-2 md:pt-8">
                <h1 class="hidden md:block text-4xl font-semibold font-grotesk border-b-4 border-black pb-3 uppercase"><?= __('app_title') ?></h1>

                <nav class="flex flex-col justify-between mt-0 md:mt-8 text-lg">
                    <div class="flex flex-col gap-5 md:gap-6 font-bold font-mono">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-house"></i> <?= __('nav_dashboard') ?></a>
                            <a href="<?= BASE_URL ?>/pages/games.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-gamepad"></i> <?= __('nav_library') ?></a>
                            <a href="<?= BASE_URL ?>/pages/entries.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-layer-group"></i> <?= __('nav_my_entries') ?></a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-house"></i> <?= __('nav_home') ?></a>
                            <a href="<?= BASE_URL ?>/pages/login.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-arrow-right-to-bracket"></i> <?= __('nav_login') ?></a>
                            <a href="<?= BASE_URL ?>/pages/register.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-user-plus"></i> <?= __('nav_register') ?></a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>

            <div class="border-t-4 border-black bg-white p-6 md:px-8 md:py-5">
                <?php if (isLoggedIn()): ?>
                    <div class="flex justify-between items-center mb-4">
                        <p class="font-mono font-bold text-sm uppercase"><?= __('nav_user') ?>: <span class="uppercase text-custom-red"><?= htmlspecialchars($_SESSION['username']) ?></span></p>
                        <a href="<?= $languageUrl ?>" class="custom-btn !px-2 !py-1">
                            <?= $current_lang === 'lv' ? 'EN' : 'LV' ?>
                        </a>
                    </div>
                    <a href="<?= BASE_URL ?>/actions/logout.php" class="w-full custom-btn bg-black !text-white hover:!bg-custom-red"><?= __('nav_logout') ?></a>
                <?php else: ?>
                    <div class="flex justify-end">
                        <a href="<?= $languageUrl ?>" class="custom-btn !px-2 !py-1">
                            <?= $current_lang === 'lv' ? 'EN' : 'LV' ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <script>
        const menuBtn = document.getElementById('mobileMenuBtn');
        const menuContent = document.getElementById('navMenuContent');
        const icon = menuBtn.querySelector('i');

        // Toggle the mobile menu
        menuBtn.addEventListener('click', () => {
            menuContent.classList.toggle('hidden');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-xmark');
        });
    </script>

    <div class="flex-1 w-full min-w-0 max-w-7xl mx-auto flex flex-col min-h-screen">