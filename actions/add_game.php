<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/games.php');
    exit;
}

// Form values
$title = trim($_POST['title'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$release_year = intval($_POST['release_year'] ?? 0);
$image_url = trim($_POST['image_url'] ?? '');
$description = trim($_POST['description'] ?? '');

// Check if all fields are filled
if (empty($title) || empty($genre) || empty($release_year) || empty($image_url) || empty($description)) {
    setFlash('error', 'All fields required');
    header('Location: ../pages/games.php');
    exit;
}

try {
    // Try to insert into database
    $stmt = $conn->prepare('INSERT INTO games (title, description, genre, release_year, image_url) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssis', $title, $description, $genre, $release_year, $image_url);
    $stmt->execute();

    // Send back if successfull
    setFlash('success', 'Game created');
    header('Location: ../pages/games.php');
    exit;
} catch (mysqli_sql_exception $e) {
    // Log and send back with error if failed
    error_log($e->getMessage());
    setFlash('error', 'Could not save changes. Please try again.');
    header('Location: ../pages/games.php');
    exit;
}
