<?php
session_start();
require_once '../config/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/games.php');
    exit;
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
        setFlash('error', __('flash_session_not_found'));
        header("Location: ../pages/games.php");
        exit;
    }

    // Send back if successfull
    setFlash('success', __('flash_game_deleted'));
    header("Location: ../pages/games.php");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', __('flash_delete_failed'));
    header("Location: ../pages/games.php");
    exit;
}
