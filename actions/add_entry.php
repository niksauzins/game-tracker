<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/entries.php');
    exit;
}

// Get game and user id
$game_id = intval($_POST['game_id'] ?? 0);
$user_id = intval($_SESSION['user_id'] ?? 0);

$status = 'waitlist';

try {
    // Try to insert the new game entry
    $stmt = $conn->prepare('INSERT INTO game_entries (user_id, game_id, status) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $user_id, $game_id, $status);
    $stmt->execute();

    // Go to all entries
    setFlash('success', 'Game added to entries');
    header('Location: ../pages/entries.php');
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', 'Could not save changes. Please try again.');
    header('Location: ../pages/entries.php');
    exit;
}
