<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/entries.php');
    exit;
}

// Get game id
$entry_id = intval($_POST['entry_id'] ?? 0);

try {
    // Try to delete game from database
    $stmt = $conn->prepare('DELETE FROM game_entries WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $entry_id, $_SESSION['user_id']);
    $stmt->execute();

    // Send back if successfull
    setFlash('success', 'Entry removed');
    header("Location: ../pages/entries.php");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', 'Failed to remove entry. Please try again.');
    header("Location: ../pages/entries.php");
    exit;
}
