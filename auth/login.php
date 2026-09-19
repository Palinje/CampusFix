<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'maintenance') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $csrf = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
        $error = 'Invalid CSRF token.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            
            // Start session and store variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin' || $user['role'] === 'maintenance') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2">Login</button>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="mb-0">Don't have an account? <a href="register.php">Register here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
