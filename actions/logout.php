<?php
session_start();
session_destroy();
redirect('../pages/login.php');
