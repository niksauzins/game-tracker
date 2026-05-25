<?php
session_start();
require_once '../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit;
}

$username = trim($_POST['username']) ?? '';
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// All fields are required
if (empty($username) || empty($email) || empty($password)) {
    setFlash('error', __('flash_all_fields_required'));
    header('Location: ../pages/register.php');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', __('flash_invalid_email'));
    header('Location: ../pages/register.php');
    exit;
}

// Check username minimal length
if (strlen($username) < 3) {
    setFlash('error', __('flash_username_too_short'));
    header('Location: ../pages/register.php');
    exit;
}

// Check password minimal length
if (strlen($password) < 8) {
    setFlash('error', __('flash_password_too_short'));
    header('Location: ../pages/register.php');
    exit;
}

// Check if username or email are taken
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()) {
    setFlash('error', __('flash_username_email_taken'));
    header('Location: ../pages/register.php');
    exit;
}

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $email, $hash);
$stmt->execute();

// Set session variables
$_SESSION['user_id'] = $conn->insert_id;
$_SESSION['username'] = $username;
$_SESSION['role'] = 'user';

// Redirect to dashboard
setFlash('success', __('flash_register_success'));
header('Location: ../pages/dashboard.php');
exit;
