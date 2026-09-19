<?php
require_once '../includes/auth.php';
requireSuperAdmin(); // Guard - Only super admins can delete users
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $id = $_POST['id'] ?? null;

    if (!$id) {
        $_SESSION['flash_error'] = "Missing user ID.";
        header("Location: ../admin/users.php");
        exit();
    }

    // Prevent self-deletion
    if ($id == $_SESSION['user_id']) {
        $_SESSION['flash_error'] = "You cannot delete your own account.";
        header("Location: ../admin/user_view.php?id=" . urlencode($id));
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_success'] = "User successfully deleted.";
        header("Location: ../admin/users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Database error deleting user.";
        header("Location: ../admin/user_view.php?id=" . urlencode($id));
        exit();
    }
} else {
    header("Location: ../admin/users.php");
    exit();
}
