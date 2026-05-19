<?php
session_start();
require_once '../config/db.php';
requireLogin();

$pageTitle = 'Game Library';

// Get all games
$stmt = $conn->prepare('SELECT * FROM games');
$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php require_once '../includes/header.php' ?>

<main class="min-h-screen flex justify-center items-start m-6">
    <div class="max-w-5xl w-full border shadow-xl rounded-xl min-h-10 p-6 relative">

        <div class="mb-6 flex justify-between items-center border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Game Library</h1>
            <button id="openModalBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow transition duration-200">
                + Add Game
            </button>
        </div>

        <div class="max-w-5xl w-full flex flex-wrap gap-4 justify-start">
            <!-- Display message when no games found -->
            <?php if (empty($games)): ?>
                <p class="text-gray-500 italic">No games found.</p>
            <?php endif; ?>

            <!-- Loop through all games and display a card -->
            <?php foreach ($games as $game): ?>
                <div id="game-<?= htmlspecialchars($game['id']) ?>" class="relative group border rounded-lg max-w-[300px] w-full shadow-sm hover:shadow-md transition">
                    <a href="../pages/game_detail.php?id=<?= htmlspecialchars($game['id']) ?>" class="absolute inset-0 z-10"></a>

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
        </div>
    </div>
</main>

<!-- Modal with a form to add a new game -->
<div id="gameModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center transition-opacity duration-300 z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 mx-4 transform transition-all duration-300">
        <h2 class="font-bold text-2xl text-gray-800 mb-4 w-full border-b pb-3">Add new game</h2>

        <form action="../actions/add_game.php" method="POST">
            <div class="mb-3">
                <label for="title" class="block font-medium text-sm text-gray-700 mb-1">Game Title</label>
                <input type="text" name="title" id="title" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="genre" class="block font-medium text-sm text-gray-700 mb-1">Genre</label>
                    <input type="text" name="genre" id="genre" required placeholder="e.g. RPG, Action" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="release_year" class="block font-medium text-sm text-gray-700 mb-1">Release Year</label>
                    <input type="number" name="release_year" id="release_year" required min="1950" max="2050" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-3">
                <label for="image_url" class="block font-medium text-sm text-gray-700 mb-1">Image URL</label>
                <input type="text" name="image_url" id="image_url" required placeholder="https://..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-5">
                <label for="description" class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                <textarea type="text" name="description" id="description" required rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="cancelModalBtn" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow">Save Game</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Set variables
    const modal = document.getElementById('gameModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');

    // Help toggle the modal
    const toggleModal = () => modal.classList.toggle('hidden');

    // When clicked, toggle the modal
    openModalBtn.addEventListener('click', toggleModal);
    cancelModalBtn.addEventListener('click', toggleModal);

    // Close modal when clicked outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal();
    });
</script>

<?php require_once '../includes/footer.php' ?>