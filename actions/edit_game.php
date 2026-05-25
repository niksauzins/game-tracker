<?php
session_start();
require_once '../config/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/games.php');
    exit;
}

// Form values
$game_id = intval($_POST['game_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$release_year = intval($_POST['release_year'] ?? 0);
$image_url = trim($_POST['image_url'] ?? '');
$description = trim($_POST['description'] ?? '');

$_SESSION['old'] = $_POST;

// Check if all fields are filled
if (empty($title) || empty($genre) || empty($release_year) || empty($image_url) || empty($description)) {
    setFlash('error', __('flash_all_fields_required'));
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
}

// Validate image URL
if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
    setFlash('error', __('flash_invalid_image_url'));
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
}

// Check release date
if ($release_year < 1950 || $release_year > 2050) {
    setFlash('error', __('flash_invalid_release_year'));
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
}


try {
    // Update database with new values
    $stmt = $conn->prepare('UPDATE games SET title = ?, description = ?, genre = ?, release_year = ?, image_url = ? WHERE id = ?');
    $stmt->bind_param('sssisi', $title, $description, $genre, $release_year, $image_url, $game_id);
    $stmt->execute();

    unset($_SESSION['old']);

    // Send back if successfull
    setFlash('success', __('flash_game_updated'));
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', __('flash_db_error'));
    header("Location: ../pages/game_detail.php?id={$game_id}");
    exit;
}
