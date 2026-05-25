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
    setFlash('error', __('flash_game_not_found'));
    header('Location: games.php');
    exit;
}

$pageTitle = $game['title'] . ' | ' . __('app_title');
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12 flex flex-col items-center">
    <div class="w-full max-w-4xl mb-6">
        <a href="games.php" class="custom-btn bg-white text-sm"><i class="fa-solid fa-arrow-left mr-2"></i> <?= __('back_to_library') ?></a>
    </div>

    <?php renderFlash('max-w-4xl w-full') ?>

    <div class="max-w-4xl w-full custom-card p-0 flex flex-col bg-white overflow-hidden">

        <div class="relative border-b-4 border-black h-56 sm:h-80 md:h-[400px]">
            <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['title']) ?> cover" class="w-full h-full object-cover">
            <p class="absolute top-2 md:top-4 left-2 md:left-4 font-mono bg-custom-yellow font-bold uppercase px-4 py-2 custom-border text-sm md:text-lg">
                <?= htmlspecialchars($game['genre']) ?>
            </p>

            <?php if (isAdmin()): ?>
                <!-- Show edit button only for admins -->
                <div class="flex gap-3 absolute top-2 md:top-4 right-2 md:right-4">
                    <button id="showEditModalBtn" class="custom-btn !bg-custom-yellow text-xs md:text-sm">
                        <?= __('edit') ?>
                    </button>
                    <button id="showDeleteModalBtn" class="custom-btn !bg-custom-red text-xs md:text-sm">
                        <?= __('delete') ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-4 md:p-8">
            <div class="flex md:flex-row flex-col justify-between items-center border-b-4 border-black pb-2">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-grotesk font-black uppercase mb-2 md:mb-4 md:mb-1 text-center md:text-left">
                    <?= htmlspecialchars($game['title']) ?>
                </h1>
                <span class="text-2xl font-bold text-white font-mono bg-black px-3 py-1">
                    <?= htmlspecialchars($game['release_year']) ?>
                </span>
            </div>

            <p class="text-sm md:text-lg font-mono leading-relaxed whitespace-pre-line custom-card !bg-custom-bg mt-6"><?= htmlspecialchars($game['description']) ?></p>
        </div>

    </div>
</main>

<?php if (isAdmin()): ?>
    <!-- Modal to edit game info, only for admins -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
        <div class="custom-card max-w-lg w-full bg-white">
            <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-6"><?= __('modal_edit_game_title') ?></h2>

            <form action="../actions/edit_game.php" method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="game_id" id="game_id" value="<?= htmlspecialchars($game['id']) ?>">

                <div class="flex flex-col gap-1">
                    <label for="title" class="uppercase font-mono text-sm font-bold"><?= __('field_title') ?></label>
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($game['title']) ?>" required class="custom-input">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label for="genre" class="uppercase font-mono text-sm font-bold"><?= __('field_genre') ?></label>
                        <input type="text" name="genre" id="genre" value="<?= htmlspecialchars($game['genre']) ?>" required class="custom-input">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="release_year" class="uppercase font-mono text-sm font-bold"><?= __('field_year') ?></label>
                        <input type="number" name="release_year" id="release_year" value="<?= htmlspecialchars($game['release_year']) ?>" required min="1950" max="2050" class="custom-input">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="image_url" class="uppercase font-mono text-sm font-bold"><?= __('field_cover_url') ?></label>
                    <input type="text" name="image_url" id="image_url" value="<?= htmlspecialchars($game['image_url']) ?>" required placeholder="https://..." class="custom-input">
                </div>

                <div class="flex flex-col gap-1 mb-4">
                    <label for="description" class="uppercase font-mono text-sm font-bold"><?= __('field_description') ?></label>
                    <textarea type="text" name="description" id="description" required rows="3" class="custom-input"><?= htmlspecialchars($game['description']) ?></textarea>
                </div>

                <div class="flex justify-end gap-4 border-t-4 border-black pt-6">
                    <button type="button" id="cancelEditModalBtn" class="custom-btn bg-white"><?= __('cancel') ?></button>
                    <button type="submit" class="custom-btn !bg-custom-yellow text-lg"><?= __('save_changes') ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal to confirm game deletion, only for admins -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/80 flex justify-center items-center transition-opacity z-50 p-4">
        <div class="custom-card max-w-md w-full bg-white">
            <h2 class="font-grotesk text-3xl font-black uppercase border-b-4 border-black pb-2 mb-4 text-center text-custom-red"><?= __('modal_delete_title') ?></h2>

            <p class="font-mono font-bold uppercase mb-4 text-lg text-center"><?= __('delete') ?> <span class="text-white bg-black px-2 py-1"><?= htmlspecialchars($game['title']) ?></span> <?= __('modal_delete_prompt') ?></p>

            <form action="../actions/delete_game.php" method="POST" class="mt-8 mb-4">
                <input type="hidden" name="game_id" id="game_id" value="<?= htmlspecialchars($game['id']) ?>">

                <div class="flex justify-end gap-4 justify-center">
                    <button type="button" id="cancelDeleteModalBtn" class="custom-btn bg-white"><?= __('cancel') ?></button>
                    <button type="submit" class="custom-btn !bg-custom-red"><?= __('btn_confirm_delete') ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM variables
        const editModal = document.getElementById('editModal');
        const showEditModalBtn = document.getElementById('showEditModalBtn');
        const cancelEditModalBtn = document.getElementById('cancelEditModalBtn');

        const deleteModal = document.getElementById('deleteModal');
        const showDeleteModalBtn = document.getElementById('showDeleteModalBtn');
        const cancelDeleteModalBtn = document.getElementById('cancelDeleteModalBtn');

        // Modal visibility toggles
        const toggleEditModal = () => editModal.classList.toggle('hidden');
        const toggleDeleteModal = () => deleteModal.classList.toggle('hidden');

        // Event listeners for buttons
        if (showEditModalBtn) showEditModalBtn.addEventListener('click', toggleEditModal);
        if (cancelEditModalBtn) cancelEditModalBtn.addEventListener('click', toggleEditModal);

        if (showDeleteModalBtn) showDeleteModalBtn.addEventListener('click', toggleDeleteModal);
        if (cancelDeleteModalBtn) cancelDeleteModalBtn.addEventListener('click', toggleDeleteModal);

        // Event listener for clicking outside the modal
        window.addEventListener('click', (e) => {
            if (e.target === editModal) {
                toggleEditModal();
            }
            if (e.target === deleteModal) {
                toggleDeleteModal();
            }
        });
    </script>
<?php endif; ?>

<?php require_once '../includes/footer.php' ?>