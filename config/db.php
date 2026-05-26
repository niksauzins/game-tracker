<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// System constants
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost');

$host = 'localhost';
$db = 'game_tracker';
$user = 'root';
$pass = '';

// Create a database connection and set charset
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

// Thow an error if could not connect
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

require_once BASE_PATH . '/includes/helpers.php';
