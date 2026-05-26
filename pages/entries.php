<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireLogin();

$pageTitle = __('nav_my_entries') . ' | ' . __('app_title');

$userId = $_SESSION['user_id'];
$status = trim($_GET['status'] ?? '');

// Get all user added games with optional filters
if ($status) {
    $stmt = $conn->prepare('
        SELECT games.*, game_entries.id AS entry_id, game_entries.status
        FROM games
        INNER JOIN game_entries ON games.id = game_entries.game_id
        WHERE game_entries.user_id = ? AND status = ?
    ');
    $stmt->bind_param('is', $userId, $status);
} else {
    $stmt = $conn->prepare('
        SELECT games.*, game_entries.id AS entry_id, game_entries.status
        FROM games
        INNER JOIN game_entries ON games.id = game_entries.game_id
        WHERE game_entries.user_id = ?
    ');
    $stmt->bind_param('i', $userId);
}

$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$subtitle = $status ? translateStatus($status) : __('lib_subtitle');
?>

<?php require_once BASE_PATH . '/includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">

    <?php renderFlash() ?>

    <div class="custom-card flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-4xl font-grotesk font-black uppercase leading-none"><?= __('entries_title') ?></h1>
            <p class="text-sm font-bold font-mono uppercase mt-2"><?= htmlspecialchars($subtitle) ?></p>
        </div>

        <div class="flex w-full md:w-auto gap-4 flex-row flex-wrap">
            <a href="<?= BASE_URL ?>/pages/entries.php" class="custom-btn text-sm <?= $status === '' ? 'bg-custom-red' : '' ?>"><?= __('entries_filter_all') ?></a>
            <a href="?status=playing" class="custom-btn text-sm <?= $status === 'playing' ? 'bg-custom-red' : '' ?>"><?= __('entries_filter_playing') ?></a>
            <a href="?status=waitlist" class="custom-btn text-sm <?= $status === 'waitlist' ? 'bg-custom-red' : '' ?>"><?= __('entries_filter_waitlist') ?></a>
            <a href="?status=finished" class="custom-btn text-sm <?= $status === 'finished' ? 'bg-custom-red' : '' ?>"><?= __('entries_filter_finished') ?></a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Loop through all games and display a card -->
        <?php foreach ($games as $game): ?>
            <?php
            $cardData = $game;
            $cardData['is_entry'] = true;
            require BASE_PATH . '/includes/components/game_card.php';
            ?>
        <?php endforeach; ?>

        <a href="<?= BASE_URL ?>/pages/games.php" class="flex justify-center items-center flex-col bg-custom-bg h-full min-h-[300px] group border-4 border-dashed border-gray-400 hover:bg-custom-teal custom-hover hover:custom-border hover:border-solid transition-all duration-200">
            <div class="text-center text-xl font-mono font-black uppercase text-gray-400 group-hover:text-black transition-all">
                <div class="text-5xl mb-2">+</div>
                <div class="bg-white px-4 border-2 border-gray-400 text-base group-hover:border-black transition-all"><?= __('entries_add_game') ?></div>
            </div>
        </a>
    </div>
</main>

<?php require_once BASE_PATH . '/includes/footer.php' ?>