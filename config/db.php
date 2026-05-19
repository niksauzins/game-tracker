<?php

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

require_once __DIR__ . '/../includes/auth.php';
