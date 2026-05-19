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

            <!-- Show edit button only for admins -->
            <?php if (isAdmin()): ?>
                <button id="showEditModalBtn" class="absolute top-0 right-0 m-4 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded z-20">
                    Edit
                </button>
            <?php endif; ?>
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

<?php if (isAdmin()): ?>
    <!-- Modal to edit game info, only for admins -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center transition-opacity duration-300 z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 mx-4 transform transition-all duration-300">
            <h2 class="font-bold text-2xl text-gray-800 mb-4 w-full border-b pb-3">Edit Game Info</h2>

            <form action="../actions/edit_game.php" method="POST">
                <input type="hidden" name="game_id" id="game_id" value="<?= htmlspecialchars($game['id']) ?>">

                <div class="mb-3">
                    <label for="title" class="block font-medium text-sm text-gray-700 mb-1">Game Title</label>
                    <input type="text" name="title" id="title" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($game['title']) ?>">
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="genre" class="block font-medium text-sm text-gray-700 mb-1">Genre</label>
                        <input type="text" name="genre" id="genre" required placeholder="e.g. RPG, Action" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($game['genre']) ?>">
                    </div>

                    <div>
                        <label for="release_year" class="block font-medium text-sm text-gray-700 mb-1">Release Year</label>
                        <input type="number" name="release_year" id="release_year" required min="1950" max="2050" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($game['release_year']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image_url" class="block font-medium text-sm text-gray-700 mb-1">Image URL</label>
                    <input type="text" name="image_url" id="image_url" required placeholder="https://..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($game['image_url']) ?>">
                </div>

                <div class="mb-5">
                    <label for="description" class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                    <textarea type="text" name="description" id="description" required rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($game['description']) ?></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelEditModalBtn" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg shadow">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal variables
        const editModal = document.getElementById('editModal');
        const showEditModalBtn = document.getElementById('showEditModalBtn');
        const cancelEditModalBtn = document.getElementById('cancelEditModalBtn');

        // Helper to toggle visibility
        const toggleEditModal = () => editModal.classList.toggle('hidden');

        // Event Listeners to toggle
        showEditModalBtn.addEventListener('click', toggleEditModal);
        cancelEditModalBtn.addEventListener('click', toggleEditModal);

        window.addEventListener('click', (e) => {
            if (e.target === editModal) toggleEditModal();
        });
    </script>
<?php endif; ?>

<?php require_once '../includes/footer.php' ?>