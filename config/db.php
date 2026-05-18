<?php

// Create a database connection
$conn = mysqli_connect('localhost', 'root', '', 'game_tracker');

// Thow an error if could not connect
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
