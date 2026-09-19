<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['user_role'];

    if (!$id) {
        $_SESSION['flash_error'] = "Missing request ID.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    try {
        // Fetch the request to verify ownership and status
        $stmt = $pdo->prepare("SELECT * FROM maintenance_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch();

        if (!$request) {
            $_SESSION['flash_error'] = "Request not found.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Check if user has permission to delete:
        // - Admin/Maintenance can delete anything
        // - Regular users can only delete THEIR OWN requests IF the status is still 'Pending'
        $isAdmin = ($role === 'admin' || $role === 'maintenance');
        $isOwnerPending = ($request['user_id'] == $user_id && $request['status'] === 'Pending');

        if ($isAdmin || $isOwnerPending) {
            $deleteStmt = $pdo->prepare("DELETE FROM maintenance_requests WHERE id = ?");
            $deleteStmt->execute([$id]);
            
            $_SESSION['flash_success'] = "Request deleted successfully.";
        } else {
            $_SESSION['flash_error'] = "You do not have permission to delete this request. It may already be in progress.";
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Database error deleting request.";
    }

    // Redirect to the appropriate dashboard
    if ($role === 'admin' || $role === 'maintenance') {
        header("Location: ../admin/requests.php");
    } else {
        header("Location: ../user/my_requests.php");
    }
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
