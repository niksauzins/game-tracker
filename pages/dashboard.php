<?php
session_start();
require_once '../config/db.php';
requireLogin();

$pageTitle = 'Dashboard';

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
$lastPlayed = $stmt->get_result()->fetch_assoc()['title'] ?? 'None';
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">
    <?php renderFlash() ?>

    <div class="custom-card flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-4xl font-grotesk font-black uppercase leading-none">Welcome, <br><span class="text-custom-red bg-black px-3 leading-normal"><?= $_SESSION['username'] ?></span></h1>
        </div>

        <div class="flex w-full md:w-auto gap-4 flex-col sm:flex-row">
            <a href="games.php" class="custom-btn text-sm">Browse Library</a>
            <a href="games.php" class="custom-btn text-sm bg-custom-teal">My Entries</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <h2 class="col-span-full text-2xl font-grotesk uppercase font-bold leading-none">Overview</h2>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center">Total games tracked</p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $totalTracked ?></p>
        </div>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center">Games finished</p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $gamesFinished ?></p>
        </div>

        <div class="custom-card">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center">Total time played</p>
            <p class="text-center font-grotesk text-5xl font-bold mt-2"><?= $totalHours ?><span class="uppercase text-xl ml-1">hr</span></p>
        </div>

        <div class="custom-card flex flex-col justify-center items-center">
            <p class="uppercase font-mono text-gray-500 font-bold text-sm text-center">Last played</p>
            <p class="text-center font-grotesk text-xl font-bold mt-2 uppercase"><?= htmlspecialchars($lastPlayed) ?></p>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>