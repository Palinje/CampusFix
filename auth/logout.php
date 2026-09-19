<?php
// Ensure session is started before destroying
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
