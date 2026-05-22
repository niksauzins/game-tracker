<?php
session_start();
require_once '../config/db.php';
requireLogin();

$pageTitle = 'Game Library';

$search = trim($_GET['search'] ?? '');

// Get all games with optional search
if ($search) {
    $stmt = $conn->prepare('
        SELECT g.*, ge.status, ge.id AS entry_id 
        FROM games g
        LEFT JOIN game_entries ge ON g.id = ge.game_id AND ge.user_id = ?
        WHERE g.title LIKE ? OR g.genre LIKE ?
    ');
    $like = "%{$search}%";
    $stmt->bind_param('iss', $_SESSION['user_id'], $like, $like);
} else {
    $stmt = $conn->prepare('
        SELECT g.*, ge.status, ge.id AS entry_id 
        FROM games g
        LEFT JOIN game_entries ge ON g.id = ge.game_id AND ge.user_id = ?
    ');
    $stmt->bind_param('i', $_SESSION['user_id']);
}

$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">

    <?php renderFlash() ?>

    <div class="custom-card flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-4xl font-grotesk font-black uppercase leading-none">Global Library</h1>
            <p class="text-sm font-bold font-mono uppercase mt-2">All games</p>
        </div>

        <div class="flex w-full md:w-auto gap-4 flex-col sm:flex-row">
            <form action="games.php" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Search titles..." class="custom-input py-2 flex-1">
                <button type="submit" class="custom-btn bg-custom-yellow text-sm">Search</button>
            </form>

            <button id="openModalBtn" class="custom-btn bg-custom-red text-sm shrink-0">
                + Add Game
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <!-- Display message when no games found -->
        <?php if (empty($games)): ?>
            <div class="col-span-full custom-card !bg-custom-yellow text-center py-12">
                <p class="font-mono font-bold text-2xl uppercase mb-4">No games found.</p>
                <a href="games.php" class="custom-btn text-sm bg-white">Clear search</a>
            </div>
        <?php endif; ?>

        <!-- Loop through all games and display a card -->
        <?php foreach ($games as $game): ?>
            <?php
            $cardData = $game;
            require '../includes/components/game_card.php';
            ?>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modal with a form to add a new game -->
<div id="gameModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6">Add new game</h2>

        <form action="../actions/add_game.php" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label for="title" class="uppercase font-mono text-sm font-bold">Title</label>
                <input type="text" name="title" id="title" required class="custom-input">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="genre" class="uppercase font-mono text-sm font-bold">Genre</label>
                    <input type="text" name="genre" id="genre" required placeholder="e.g. RPG, Action" class="custom-input">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="release_year" class="uppercase font-mono text-sm font-bold">Year</label>
                    <input type="number" name="release_year" id="release_year" required min="1950" max="2050" class="custom-input">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label for="image_url" class="uppercase font-mono text-sm font-bold">Cover Image URL</label>
                <input type="text" name="image_url" id="image_url" required placeholder="https://..." class="custom-input">
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="description" class="uppercase font-mono text-sm font-bold">Description</label>
                <textarea type="text" name="description" id="description" required rows="3" class="custom-input"></textarea>
            </div>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="cancelModalBtn" class="custom-btn bg-white">Cancel</button>
                <button type="submit" class="custom-btn !bg-custom-teal text-lg">Save Game</button>
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
    if (openModalBtn) openModalBtn.addEventListener('click', toggleModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', toggleModal);

    // Close modal when clicked outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal();
    });
</script>

<?php require_once '../includes/footer.php' ?>