<?php
session_start();
require_once '../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../pages/login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Set old values and unset password
$_SESSION['old'] = $_POST;
unset($_SESSION['old']['password']);


// All fields are required
if (empty($email) || empty($password)) {
    redirect('../pages/login.php', 'error', __('flash_all_fields_required'));
}

// Find the user with the correct email
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// If user with that email doesn't exist or the password doesn't match show an error
if (!$user || !password_verify($password, $user['password'])) {
    redirect('../pages/login.php', 'error', __('flash_invalid_credentials'));
}

unset($_SESSION['old']);

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

redirect('../pages/dashboard.php', 'success', __('flash_login_success'));
