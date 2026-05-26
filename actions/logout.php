<?php
require_once __DIR__ . '/../config/db.php';
session_start();
session_destroy();
redirect('/pages/login.php');
