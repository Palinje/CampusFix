<?php
// Ensure session is started before checking session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure database configuration (and BASE_URL) is loaded
require_once __DIR__ . '/../config/db.php';

// 1. Basic Authentication Guard
// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    // Using absolute path from web root to ensure it works regardless of which folder includes this file
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// 2. Admin/Maintenance Guard Helper
// Checks if the logged-in user has admin or maintenance privileges
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isset($_SESSION['user_role']) || 
            ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'maintenance')) {
            // Redirect regular users back to their dashboard if they try to access admin pages
            header("Location: " . BASE_URL . "/user/dashboard.php");
            exit();
        }
    }
}

// 2b. Super Admin Guard Helper
// Checks if the logged-in user is strictly an 'admin'
if (!function_exists('requireSuperAdmin')) {
    function requireSuperAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash_error'] = "Access Denied: Super Admin privileges required.";
            header("Location: " . BASE_URL . "/admin/dashboard.php");
            exit();
        }
    }
}

// 3. CSRF Protection
if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = "Invalid CSRF token. Please try again.";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php'));
            exit();
        }
    }
}
