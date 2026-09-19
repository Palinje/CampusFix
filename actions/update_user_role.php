<?php
require_once '../includes/auth.php';
requireSuperAdmin(); // Guard - Only super admins can update user roles
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $id = $_POST['id'] ?? null;
    $role = $_POST['role'] ?? null;
    $valid_roles = ['student', 'teacher', 'staff', 'maintenance', 'admin'];

    if (!$id || !$role || !in_array($role, $valid_roles)) {
        $_SESSION['flash_error'] = "Invalid data provided.";
        header("Location: ../admin/users.php");
        exit();
    }

    // Prevent self-demotion
    if ($id == $_SESSION['user_id']) {
        $_SESSION['flash_error'] = "You cannot change your own role.";
        header("Location: ../admin/user_view.php?id=" . urlencode($id));
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);

        $_SESSION['flash_success'] = "User role updated successfully.";
        header("Location: ../admin/user_view.php?id=" . urlencode($id));
        exit();
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Database error updating user role.";
        header("Location: ../admin/user_view.php?id=" . urlencode($id));
        exit();
    }
} else {
    header("Location: ../admin/users.php");
    exit();
}
