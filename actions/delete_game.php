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

    // Send back if successfull
    setFlash('success', 'Game deleted');
    header("Location: ../pages/games.php");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', 'Failed to delete game. Please try again.');
    header("Location: ../pages/games.php");
    exit;
}
