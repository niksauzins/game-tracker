<?php
session_start();
require_once '../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// All fields are required
if (empty($email) || empty($password)) {
    setFlash('error', __('flash_all_fields_required'));
    header('Location: ../pages/login.php');
    exit;
}

// Find the user with the correct email
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// If user with that email doesn't exist or the password doesn't match show an error
if (!$user || !password_verify($password, $user['password'])) {
    setFlash('error', __('flash_invalid_credentials'));
    header('Location: ../pages/login.php');
    exit;
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

setFlash('success', __('flash_login_success'));
header('Location: ../pages/dashboard.php');
exit;
