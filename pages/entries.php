<?php
session_start();
require_once '../config/db.php';
requireLogin();

$pageTitle = 'My Entries';

// Get all user added games
$stmt = $conn->prepare('
    SELECT games.*, game_entries.id AS entry_id, game_entries.status
    FROM games
    INNER JOIN game_entries ON games.id = game_entries.game_id
    WHERE game_entries.user_id = ?
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// TODO: Status filter
?>

<?php require_once '../includes/header.php' ?>

<main class="flex-1 p-6 lg:p-12">

    <div class="custom-card flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h1 class="text-4xl font-grotesk font-black uppercase leading-none">My entries</h1>
            <p class="text-sm font-bold font-mono uppercase mt-2">Active games</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Loop through all games and display a card -->
        <?php foreach ($games as $game): ?>
            <?php
            $cardData = $game;
            $cardData['is_entry'] = true;
            require '../includes/components/game_card.php';
            ?>
        <?php endforeach; ?>

        <a href="../pages/games.php" class="flex justify-center items-center flex-col bg-custom-bg h-full min-h-[300px] group border-4 border-dashed border-gray-400 hover:bg-custom-teal custom-hover hover:custom-border hover:border-solid transition-all duration-200">
            <div class="text-center text-xl font-mono font-black uppercase text-gray-400 group-hover:text-black transition-all">
                <div class="text-5xl mb-2">+</div>
                <div class="bg-white px-4 border-2 border-gray-400 text-base group-hover:border-black transition-all">Add game</div>
            </div>
        </a>
    </div>
</main>

<?php require_once '../includes/footer.php' ?>