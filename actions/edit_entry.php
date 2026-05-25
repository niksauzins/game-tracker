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
$status = trim($_POST['status'] ?? 'waitlist');
$rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? intval($_POST['rating']) : null;
$started_at = !empty($_POST['started_at']) ? $_POST['started_at'] : null;
$finished_at = !empty($_POST['finished_at']) ? $_POST['finished_at'] : null;
$notes = trim($_POST['notes'] ?? '');

$_SESSION['old'] = $_POST;
$_SESSION['open_modal'] = 'edit_entry';

// Validate data
$allowed_statuses = ['waitlist', 'playing', 'finished', 'quit'];
if (!in_array($status, $allowed_statuses)) {
    setFlash('error', __('flash_invalid_status'));
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}

// Finished at should be after started at
if ($started_at && $finished_at && $started_at > $finished_at) {
    setFlash('error', __('flash_date_order_invalid'));
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}


try {
    // Update database values
    $stmt = $conn->prepare('
    UPDATE game_entries 
    SET status = ?, rating = ?, started_at = ?, finished_at = ?, notes = ? 
    WHERE id = ? AND user_id = ?
    ');
    $stmt->bind_param('sisssii', $status, $rating, $started_at, $finished_at, $notes, $entry_id, $_SESSION['user_id']);
    $stmt->execute();
    
    unset($_SESSION['old'], $_SESSION['open_modal']);
    
    setFlash('success', __('flash_entry_updated'));
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    setFlash('error', __('flash_db_error'));
    header("Location: ../pages/entry_detail.php?id={$entry_id}");
    exit;
}
