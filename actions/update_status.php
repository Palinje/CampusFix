<?php
require_once '../includes/auth.php';
requireAdmin(); // Guard - Only admin and maintenance can update status
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$status) {
        $_SESSION['flash_error'] = "Missing information.";
        header("Location: ../admin/requests.php");
        exit();
    }

    try {
        $completedBy = $status === 'Completed' ? $_SESSION['user_id'] : null;
        $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = ?, completed_by = ? WHERE id = ?");
        $stmt->execute([$status, $completedBy, $id]);

        $_SESSION['flash_success'] = "Status updated successfully.";
        header("Location: ../admin/request_view.php?id=" . urlencode($id));
        exit();
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Database error updating status.";
        header("Location: ../admin/request_view.php?id=" . urlencode($id));
        exit();
    }
} else {
    header("Location: ../admin/requests.php");
    exit();
}
