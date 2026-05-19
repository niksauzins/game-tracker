<?php
session_start();
require_once '../config/db.php';
requireLogin();

// Get game id from url
$game_id = intval($_GET['id'] ?? 0);

// Get the single game from database
$stmt = $conn->prepare('SELECT * FROM games WHERE id = ?');
$stmt->bind_param('i', $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

// If no game found, go back
if (!$game) {
    header('Location: ../pages/games.php?error=game_not_found');
    exit;
}

$pageTitle = $game['title'];
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-start m-6">
    <div class="max-w-3xl w-full border shadow-xl rounded-xl relative pb-6 bg-white overflow-hidden">

        <div class="relative border-b border-gray-200 overflow-hidden h-[400px]">
            <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?> cover image" class="w-full h-full object-cover">
            <p class="absolute bottom-0 left-0 m-4 text-sm font-semibold text-white bg-black/70 px-3 py-1.5 rounded z-20">
                <?= htmlspecialchars($game['genre']) ?>
            </p>
        </div>

        <div class="px-6 mt-6">
            <div class="flex md:flex-row flex-col justify-between items-center border-b pb-4">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-4 md:mb-1">
                    <?= htmlspecialchars($game['title']) ?>
                </h1>
                <span class="text-lg font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    <?= htmlspecialchars($game['release_year']) ?>
                </span>
            </div>

            <p class="text-gray-700 leading-relaxed text-base whitespace-pre-line">
                <?= htmlspecialchars($game['description']) ?>
            </p>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php' ?>