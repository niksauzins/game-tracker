<?php
session_start();
require_once '../config/db.php';
requireLogin();

$pageTitle = 'My Entries';

// Get all user added games
$stmt = $conn->prepare('
    SELECT games.*, game_entries.id AS entry_id
    FROM games
    INNER JOIN game_entries ON games.id = game_entries.game_id
    WHERE game_entries.user_id = ?
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-start m-6">
    <div class="max-w-5xl w-full border shadow-xl rounded-xl min-h-10 p-6 relative">

        <div class="mb-6 flex justify-between items-center border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">My Entries</h1>
        </div>

        <div class="max-w-5xl w-full flex flex-wrap gap-4 justify-start">
            <!-- Display message when no games found -->
            <?php if (empty($games)): ?>
                <p class="text-gray-500 italic">No games found.</p>
            <?php endif; ?>

            <!-- Loop through all games and display a card -->
            <?php foreach ($games as $game): ?>
                <div id="game-<?= htmlspecialchars($game['id']) ?>" class="relative group border rounded-lg max-w-[300px] w-full shadow-sm hover:shadow-md transition">
                    <a href="../pages/entry_detail.php?id=<?= htmlspecialchars($game['entry_id']) ?>" class="absolute inset-0 z-10"></a>

                    <div class="relative mb-2 border-b border-gray-200 overflow-hidden rounded-t-lg h-[180px]">
                        <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?> cover image" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <p class="absolute top-0 left-0 m-2 text-xs text-white bg-black/70 px-2 py-1 rounded z-20"><?= htmlspecialchars($game['genre']) ?></p>
                    </div>

                    <div class="px-4 pb-4">
                        <h2 class="flex justify-between items-center font-semibold text-lg mb-1">
                            <?= htmlspecialchars($game['title']) ?>
                            <span class="font-normal text-sm text-gray-500"><?= htmlspecialchars($game['release_year']) ?></span>
                        </h2>
                        <p class="text-sm text-gray-600 line-clamp-3"><?= htmlspecialchars($game['description']) ?></p>
                    </div>

                </div>
            <?php endforeach; ?>

            <div class="relative flex justify-center items-center flex-col group border-2 border-dashed border-gray-400 rounded-lg max-w-[300px] w-full min-h-[300px] shadow-sm hover:shadow-md transition bg-gray-200">
                <a href="../pages/games.php" class="absolute inset-0 z-10"></a>

                <div class="text-3xl font-bold text-gray-500">+</div>

                <div class="font-medium text-gray-500">New Entry</div>

            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>