<?php
session_start();
require_once '../config/db.php';
requireAdmin();

// Form values
$game_id = intval($_POST['game_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$release_year = intval($_POST['release_year'] ?? 0);
$image_url = trim($_POST['image_url'] ?? '');
$description = trim($_POST['description'] ?? '');

// Check if all fields are filled
if (empty($title) || empty($genre) || empty($release_year) || empty($image_url) || empty($description)) {
    header("Location: ../pages/game_detail.php?id={$game_id}&error=All+fields+required");
    exit;
}

try {
    // Update database with new values
    $stmt = $conn->prepare('UPDATE games SET title = ?, description = ?, genre = ?, release_year = ?, image_url = ? WHERE id = ?');
    $stmt->bind_param('sssisi', $title, $description, $genre, $release_year, $image_url, $game_id);
    $stmt->execute();

    // Send back if successfull
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
}
