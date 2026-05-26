<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../pages/entries.php');
}

// Get game id
$entry_id = intval($_POST['entry_id'] ?? 0);

try {
    // Try to delete game from database
    $stmt = $conn->prepare('DELETE FROM game_entries WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $entry_id, $_SESSION['user_id']);
    $stmt->execute();

    // Check if any rows were deleted
    if ($stmt->affected_rows === 0) {
        redirect("../pages/entries.php", 'error', __('flash_entry_not_found'));
    }

    // Send back if successfull
    redirect("../pages/entries.php", 'success', __('flash_entry_removed'));
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    redirect("../pages/entries.php", 'error', __('flash_remove_failed'));
}
