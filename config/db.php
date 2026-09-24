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

try {
    $columnCheck = $pdo->query("SHOW COLUMNS FROM maintenance_requests LIKE 'completed_by'");
    if (!$columnCheck->fetch()) {
        $pdo->exec("ALTER TABLE maintenance_requests ADD COLUMN completed_by INT NULL");
    }

    $imageColumnCheck = $pdo->query("SHOW COLUMNS FROM maintenance_requests LIKE 'image_path'");
    if (!$imageColumnCheck->fetch()) {
        $pdo->exec("ALTER TABLE maintenance_requests ADD COLUMN image_path VARCHAR(255) NULL");
    }
} catch (PDOException $e) {
    die("Database setup failed. Please try again later.");
}
