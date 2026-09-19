<?php
require_once '../includes/auth.php'; // Guard
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    $user_id = $_SESSION['user_id'];
    $location = trim($_POST['location'] ?? '');
    $problem_type = trim($_POST['problem_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = 'Pending'; // Default status for new requests

    // Basic validation
    if (empty($location) || empty($problem_type) || empty($description)) {
        $_SESSION['flash_error'] = "All fields are required.";
        header("Location: ../user/submit_request.php");
        exit();
    }

    try {
        // Prepare and execute the PDO insert statement
        $stmt = $pdo->prepare("INSERT INTO maintenance_requests (user_id, location, problem_type, description, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $location, $problem_type, $description, $status]);
        
        // Redirect on success
        $_SESSION['flash_success'] = "Maintenance request submitted successfully.";
        header("Location: ../user/my_requests.php");
        exit();
    } catch (PDOException $e) {
        // For development, you might log the error. Redirecting back to form with error for the user.
        $_SESSION['flash_error'] = "Database error. Please try again later.";
        header("Location: ../user/submit_request.php");
        exit();
    }
} else {
    // Redirect if someone tries to access this script via GET
    header("Location: ../user/submit_request.php");
    exit();
}
