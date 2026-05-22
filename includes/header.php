<?php
require_once __DIR__ . '/../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GameTracker' ?></title>

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
</head>

<body class="min-h-screen flex flex-col md:flex-row text-black relative">
    <aside class="w-full md:max-w-72 bg-custom-yellow flex flex-col border-b-4 md:border-b-0 md:border-r-4 border-black z-40 md:sticky md:top-0 md:h-screen shrink-0">

        <!-- Mobile navbar -->
        <div class="flex items-center justify-between p-4 md:hidden">
            <h1 class="text-3xl font-black font-grotesk tracking-tight">GAME TRACKER</h1>
            <button id="mobileMenuBtn" class="custom-btn !px-3 !py-1.5 bg-white text-sm">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Desktop navbar -->
        <div id="navMenuContent" class="hidden md:flex flex-col flex-1">
            <div class="flex-1 p-6 md:p-8 pt-2 md:pt-8">
                <h1 class="hidden md:block text-4xl font-semibold font-grotesk border-b-4 border-black pb-3">GAME TRACKER</h1>

                <nav class="flex flex-col justify-between mt-0 md:mt-8 text-lg">
                    <div class="flex flex-col gap-5 md:gap-6 font-bold font-mono">
                        <?php if (isLoggedIn()): ?>
                            <a href="/pages/dashboard.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-house"></i> Dashboard</a>
                            <a href="/pages/games.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-gamepad"></i> Library</a>
                            <a href="/pages/entries.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-layer-group"></i> My Entries</a>
                        <?php else: ?>
                            <a href="/" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-house"></i> Home</a>
                            <a href="/pages/login.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login</a>
                            <a href="/pages/register.php" class="hover:text-custom-red hover:translate-x-2 transition"><i class="fa-solid fa-user-plus"></i> Register</a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>

            <?php if (isLoggedIn()): ?>
                <div class="border-t-4 border-black bg-white p-6 md:p-8">
                    <p class="mb-4 font-mono font-bold text-sm">USER: <span class="uppercase text-custom-red"><?= htmlspecialchars($_SESSION['username']) ?></span></p>
                    <a href="../actions/logout.php" class="w-full custom-btn bg-black !text-white hover:!bg-custom-red">Logout</a>
                </div>
            <?php endif; ?>
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