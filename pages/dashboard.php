<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireLogin();

$pageTitle = __('nav_dashboard') . ' | ' . __('app_title');

$userId = $_SESSION['user_id'];

// Get total tracked games
$stmt = $conn->prepare('SELECT COUNT(*) AS total_tracked FROM game_entries WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$totalTracked = $stmt->get_result()->fetch_assoc()['total_tracked'] ?? 0;

// Get number of finished games
$stmt = $conn->prepare("SELECT COUNT(*) AS games_finished FROM game_entries WHERE user_id = ? AND status = 'finished'");
$stmt->bind_param('i', $userId);
$stmt->execute();
$gamesFinished = $stmt->get_result()->fetch_assoc()['games_finished'] ?? 0;

// Get total amount of time played from session length
$stmt = $conn->prepare('
    SELECT SUM(s.duration_minutes) AS minutes
    FROM sessions s
    JOIN game_entries ge ON s.entry_id = ge.id
    WHERE ge.user_id = ?
');
$stmt->bind_param('i', $userId);
$stmt->execute();
$totalMinutes = $stmt->get_result()->fetch_assoc()['minutes'] ?? 0;
$totalHours = round($totalMinutes / 60, 1);

// Get last game played
$stmt = $conn->prepare('
    SELECT g.title
    FROM games g
    JOIN game_entries ge ON ge.game_id = g.id
    JOIN sessions s ON s.entry_id = ge.id
    WHERE ge.user_id = ?
    ORDER BY s.played_at DESC, s.id DESC
    LIMIT 1
');
$stmt->bind_param('i', $userId);
$stmt->execute();
$lastPlayed = $stmt->get_result()->fetch_assoc()['title'] ?? __('none');
?>

<?php require_once BASE_PATH . '/includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">
    <?php renderFlash() ?>

    <div class="custom-card flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-grotesk font-black uppercase leading-none"><?= __('welcome') ?> <br class="hidden sm:block"><span class="text-custom-red bg-black px-3 py-1 inline-block mt-0 leading-normal"><?= $_SESSION['username'] ?></span></h1>
        </div>

        <div class="flex w-full md:w-auto gap-4 flex-col sm:flex-row">
            <a href="<?= BASE_URL ?>/pages/games.php" class="custom-btn text-sm"><?= __('browse_library') ?></a>
            <a href="<?= BASE_URL ?>/pages/entries.php" class="custom-btn text-sm bg-custom-teal"><?= __('my_entries') ?></a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <h2 class="col-span-full text-2xl font-grotesk uppercase font-bold leading-none"><?= __('overview') ?></h2>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center"><?= __('total_tracked') ?></p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $totalTracked ?></p>
        </div>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center"><?= __('games_finished') ?></p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $gamesFinished ?></p>
        </div>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center"><?= __('total_time') ?></p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $totalHours ?><span class="uppercase text-xl ml-1"><?= __('hours_short') ?></span></p>
        </div>

        <div class="custom-card flex flex-col justify-center items-center">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center"><?= __('last_played') ?></p>
            <p class="text-center font-grotesk text-xl font-bold mt-2 uppercase"><?= htmlspecialchars($lastPlayed) ?></p>
        </div>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/footer.php' ?>