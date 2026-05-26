<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/games.php');
}

// Form values
$title = trim($_POST['title'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$release_year = intval($_POST['release_year'] ?? 0);
$image_url = trim($_POST['image_url'] ?? '');
$description = trim($_POST['description'] ?? '');

$_SESSION['old'] = $_POST;

// Check if all fields are filled
if (empty($title) || empty($genre) || empty($release_year) || empty($image_url) || empty($description)) {
    redirect('/pages/games.php', 'error', __('flash_all_fields_required'));
}

// Validate image URL
if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
    redirect('/pages/games.php', 'error', __('flash_invalid_image_url'));
}

// Check release date
if ($release_year < 1950 || $release_year > 2050) {
    redirect('/pages/games.php', 'error', __('flash_invalid_release_year'));
}


try {
    // Try to insert into database
    $stmt = $conn->prepare('INSERT INTO games (title, description, genre, release_year, image_url) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssis', $title, $description, $genre, $release_year, $image_url);
    $stmt->execute();

    unset($_SESSION['old']);

    // Send back if successfull
    redirect('/pages/games.php', 'success', __('flash_game_created'));
} catch (mysqli_sql_exception $e) {
    // Log and send back with error if failed
    error_log($e->getMessage());
    redirect('/pages/games.php', 'error', __('flash_db_error'));
}
