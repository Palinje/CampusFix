<?php
$host = 'localhost';
$db   = 'school_maintenance_db';
$user = 'root';
$pass = '';

// Define base URL for absolute paths
define('BASE_URL', '/CampusFix');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed. Please try again later.");
}
