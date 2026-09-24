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
    $imagePath = null;

    // Basic validation
    if (empty($location) || empty($problem_type) || empty($description)) {
        $_SESSION['flash_error'] = "All fields are required.";
        header("Location: ../user/submit_request.php");
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || $_FILES['image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['flash_error'] = "The picture must be smaller than 5 MB.";
            header("Location: ../user/submit_request.php");
            exit();
        }

        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        if ($imageInfo === false || !isset($allowedMimeTypes[$imageInfo['mime']])) {
            $_SESSION['flash_error'] = "Please upload a valid JPG, PNG, GIF, or WebP picture.";
            header("Location: ../user/submit_request.php");
            exit();
        }

        $uploadDirectory = __DIR__ . '/../assets/uploads';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            $_SESSION['flash_error'] = "The picture could not be uploaded. Please try again.";
            header("Location: ../user/submit_request.php");
            exit();
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$imageInfo['mime']];
        $imagePath = 'assets/uploads/' . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory . '/' . $fileName)) {
            $_SESSION['flash_error'] = "The picture could not be uploaded. Please try again.";
            header("Location: ../user/submit_request.php");
            exit();
        }
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
        $stmt = $pdo->prepare("INSERT INTO maintenance_requests (user_id, location, problem_type, description, status, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $location, $problem_type, $description, $status, $imagePath]);
        
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
