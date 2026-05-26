<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/register.php');
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Set old values and unset password
$_SESSION['old'] = $_POST;
unset($_SESSION['old']['password']);

// All fields are required
if (empty($username) || empty($email) || empty($password)) {
    redirect('/pages/register.php', 'error', __('flash_all_fields_required'));
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect('/pages/register.php', 'error', __('flash_invalid_email'));
}

// Check username minimal length
if (strlen($username) < 3) {
    redirect('/pages/register.php', 'error', __('flash_username_too_short'));
}

// Check password minimal length
if (strlen($password) < 8) {
    redirect('/pages/register.php', 'error', __('flash_password_too_short'));
}


// Check if username or email are taken
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()) {
    redirect('/pages/register.php', 'error', __('flash_username_email_taken'));
}

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $email, $hash);
$stmt->execute();

unset($_SESSION['old']);

// Set session variables
$_SESSION['user_id'] = $conn->insert_id;
$_SESSION['username'] = $username;
$_SESSION['role'] = 'user';

// Redirect to dashboard
redirect('/pages/dashboard.php', 'success', __('flash_register_success'));
