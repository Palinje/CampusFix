<?php
// Ensure session is started before destroying
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// Destroy the session completely
session_destroy();

// Redirect to home/login page
header("Location: /CampusFix/index.php");
exit();
