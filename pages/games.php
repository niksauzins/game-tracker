<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireLogin();

$pageTitle = __('lib_title') . ' | ' . __('app_title');

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

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$openModal = !empty($old);
?>

<?php require_once BASE_PATH . '/includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">

    <?php if (!$openModal) renderFlash() ?>

    <div class="custom-card flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-6 mb-8">
        <div>
            <h1 class="text-3xl lg:text-4xl font-grotesk font-black uppercase leading-none"><?= __('lib_title') ?></h1>
            <p class="text-sm font-bold font-mono uppercase mt-2"><?= __('lib_subtitle') ?></p>
        </div>

        <div class="flex w-full md:w-auto gap-4 flex-col sm:flex-row">
            <form action="<?= BASE_URL ?>/pages/games.php" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="<?= $search ?>" placeholder="<?= __('lib_search_placeholder') ?>" class="custom-input py-2 flex-1 w-full min-w-0">
                <button type="submit" class="custom-btn bg-custom-yellow text-xs lg:text-sm shrink-0"><?= __('lib_search') ?></button>
            </form>

            <button id="openModalBtn" class="custom-btn bg-custom-red text-sm shrink-0">
                <?= __('lib_add_game_btn') ?>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <!-- Display message when no games found -->
        <?php if (empty($games)): ?>
            <div class="col-span-full custom-card !bg-custom-yellow text-center py-12">
                <p class="font-mono font-bold text-2xl uppercase mb-4"><?= __('lib_no_games_found') ?></p>
                <a href="<?= BASE_URL ?>/pages/games.php" class="custom-btn text-sm bg-white"><?= __('lib_clear_search') ?></a>
            </div>
        <?php endif; ?>

        <!-- Loop through all games and display a card -->
        <?php foreach ($games as $game): ?>
            <?php
            $cardData = $game;
            require BASE_PATH . '/includes/components/game_card.php';
            ?>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modal with a form to add a new game -->
<div id="gameModal" class="<?= $openModal ? '' : 'hidden' ?> fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
    <div class="custom-card max-w-lg w-full bg-white">
        <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6"><?= __('modal_add_game_title') ?></h2>

        <form action="<?= BASE_URL ?>/actions/add_game.php" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label for="title" class="uppercase font-mono text-sm font-bold"><?= __('field_title') ?></label>
                <input type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" id="title" required class="custom-input">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="genre" class="uppercase font-mono text-sm font-bold"><?= __('field_genre') ?></label>
                    <input type="text" name="genre" value="<?= htmlspecialchars($old['genre'] ?? '') ?>" id="genre" required placeholder="<?= __('field_genre_placeholder') ?>" class="custom-input">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="release_year" class="uppercase font-mono text-sm font-bold"><?= __('field_year') ?></label>
                    <input type="number" name="release_year" value="<?= htmlspecialchars($old['release_year'] ?? '') ?>" id="release_year" required min="1950" max="2050" class="custom-input">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label for="image_url" class="uppercase font-mono text-sm font-bold"><?= __('field_cover_url') ?></label>
                <input type="text" name="image_url" value="<?= htmlspecialchars($old['image_url'] ?? '') ?>" id="image_url" required placeholder="https://..." class="custom-input">
            </div>

            <div class="flex flex-col gap-1 mb-4">
                <label for="description" class="uppercase font-mono text-sm font-bold"><?= __('field_description') ?></label>
                <textarea name="description" id="description" required rows="3" class="custom-input"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            </div>

            <?php renderFlash('-mt-2') ?>

            <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                <button type="button" id="cancelModalBtn" class="custom-btn bg-white"><?= __('cancel') ?></button>
                <button type="submit" class="custom-btn !bg-custom-teal text-lg"><?= __('btn_save_game') ?></button>
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

<?php require_once BASE_PATH . '/includes/footer.php' ?>