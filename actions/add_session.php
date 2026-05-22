<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/entries.php');
    exit;
}

// Form values
$entry_id = intval($_POST['entry_id'] ?? 0);
$played_at = trim($_POST['played_at'] ?? '');
$duration_minutes = intval($_POST['duration_minutes'] ?? 0);
$notes = trim($_POST['notes'] ?? null);
if ($notes === '') {
    $notes = null;
}

// Validate
if ($entry_id <= 0 || empty($played_at) || $duration_minutes <= 0) {
    setFlash('error', 'Invalid session inputs');
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}

// Make sure the entry belongs to the correct user
$stmt = $conn->prepare('SELECT id FROM game_entries WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $entry_id, $_SESSION['user_id']);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    setFlash('error', 'Unauthorized');
    header('Location: ../pages/entries.php');
    exit;
}

try {
    // Insert session data into database
    $stmt = $conn->prepare('INSERT INTO sessions (entry_id, played_at, duration_minutes, notes) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isis', $entry_id, $played_at, $duration_minutes, $notes);
    $stmt->execute();

    setFlash('success', 'Session added');
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', 'Could not save changes. Please try again.');
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}
