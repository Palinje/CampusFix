<?php
// Ensure session is started before checking session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Basic Authentication Guard
// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    // Using absolute path from web root to ensure it works regardless of which folder includes this file
    header("Location: /CampusFix/auth/login.php");
    exit();
}

// 2. Admin/Maintenance Guard Helper
// Checks if the logged-in user has admin or maintenance privileges
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'maintenance')) {
            // Redirect regular users back to their dashboard if they try to access admin pages
            header("Location: /CampusFix/user/dashboard.php");
            exit();
        }
    }
}
