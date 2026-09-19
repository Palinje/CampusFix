<?php
require_once '../includes/auth.php';
requireAdmin(); // Guard - Only admin and maintenance can update status
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$status) {
        header("Location: ../admin/requests.php?error=Missing information.");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        header("Location: ../admin/request_view.php?id=" . urlencode($id) . "&success=Status updated successfully.");
        exit();
    } catch (PDOException $e) {
        header("Location: ../admin/request_view.php?id=" . urlencode($id) . "&error=Database error updating status.");
        exit();
    }
} else {
    header("Location: ../admin/requests.php");
    exit();
}
