<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/games.php');
}

// Get game id
$game_id = intval($_POST['game_id'] ?? 0);

try {
    // Try to delete game from database
    $stmt = $conn->prepare('DELETE FROM games WHERE id = ?');
    $stmt->bind_param('i', $game_id);
    $stmt->execute();

    // Check if any rows were deleted
    if ($stmt->affected_rows === 0) {
        redirect("/pages/games.php", 'error', __('flash_session_not_found'));
    }

    // Send back if successfull
    redirect("/pages/games.php", 'success', __('flash_game_deleted'));
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    redirect("/pages/games.php", 'error', __('flash_delete_failed'));
}
