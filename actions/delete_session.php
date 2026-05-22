<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/entries.php');
    exit;
}

// Get game id
$session_id = intval($_POST['session_id'] ?? 0);
$entry_id = intval($_POST['entry_id'] ?? 0);

try {
    // Try to delete game from database
    $stmt = $conn->prepare('
        DELETE s 
        FROM sessions s
        INNER JOIN game_entries ge ON s.entry_id = ge.id
        WHERE s.id = ? AND ge.user_id = ?
    ');
    $stmt->bind_param('ii', $session_id, $_SESSION['user_id']);
    $stmt->execute();

    // Send back if successfull
    setFlash('success', 'Session deleted');
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', 'Failed to delete session. Please try again.');
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}
