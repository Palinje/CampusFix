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
        $dailyCountStmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_requests WHERE user_id = ? AND DATE(created_at) = CURDATE()");
        $dailyCountStmt->execute([$user_id]);

        if ((int) $dailyCountStmt->fetchColumn() >= 2) {
            $_SESSION['flash_error'] = "You can submit only two maintenance requests per day.";
            header("Location: ../user/submit_request.php");
            exit();
        }

        $dailyTypeStmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_requests WHERE user_id = ? AND problem_type = ? AND DATE(created_at) = CURDATE()");
        $dailyTypeStmt->execute([$user_id, $problem_type]);

        if ((int) $dailyTypeStmt->fetchColumn() >= 1) {
            $_SESSION['flash_error'] = "Your requests for the day must have different problem types.";
            header("Location: ../user/submit_request.php");
            exit();
        }

        $issueCountStmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_requests WHERE location = ? AND problem_type = ?");
        $issueCountStmt->execute([$location, $problem_type]);

        if ((int) $issueCountStmt->fetchColumn() >= 15) {
            $_SESSION['flash_error'] = "This issue has reached the maximum of 15 requests.";
            header("Location: ../user/submit_request.php");
            exit();
        }

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
