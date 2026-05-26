<?php
session_start();
require_once __DIR__ . '/../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/entries.php');
}

// Form values
$entry_id = intval($_POST['entry_id'] ?? 0);
$played_at = trim($_POST['played_at'] ?? '');
$duration_minutes = intval($_POST['duration_minutes'] ?? 0);
$notes = trim($_POST['notes'] ?? null);
if ($notes === '') {
    $notes = null;
}

$_SESSION['old'] = $_POST;
$_SESSION['open_modal'] = 'add_session';

// Validate
if ($entry_id <= 0 || empty($played_at) || $duration_minutes <= 0) {
    redirect("/pages/entry_detail.php?id={$entry_id}", 'error', __('flash_invalid_session_inputs'));
}

// Don't allow future dates
if (!empty($played_at) && $played_at > date('Y-m-d')) {
    redirect("/pages/entry_detail.php?id={$entry_id}", 'error', __('flash_session_future_date'));
}


// Make sure the entry belongs to the correct user
$stmt = $conn->prepare('SELECT id FROM game_entries WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $entry_id, $_SESSION['user_id']);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    redirect('/pages/entries.php', 'error', __('flash_unauthorized'));
}

try {
    // Insert session data into database
    $stmt = $conn->prepare('INSERT INTO sessions (entry_id, played_at, duration_minutes, notes) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isis', $entry_id, $played_at, $duration_minutes, $notes);
    $stmt->execute();

    unset($_SESSION['old'], $_SESSION['open_modal']);

    redirect("/pages/entry_detail.php?id={$entry_id}", 'success', __('flash_session_added'));
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    redirect("/pages/entry_detail.php?id={$entry_id}", 'error', __('flash_db_error'));
}
