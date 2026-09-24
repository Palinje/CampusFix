<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Route logged-in users to their respective dashboards
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'maintenance') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}
?>
<?php require_once 'includes/header.php'; ?>

<style>
    body {
        background:
            radial-gradient(circle at 12% 18%, rgba(0, 120, 215, 0.18), transparent 28%),
            radial-gradient(circle at 88% 78%, rgba(255, 193, 7, 0.14), transparent 25%),
            linear-gradient(135deg, #dcecff 0%, #f9fbff 48%, #e7f3ff 100%);
    }

    /* Make text more legible over background */
    .landing-content {
        background-color: #ffffff;
        border: 1px solid rgba(0, 120, 215, 0.08);
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(24, 50, 77, 0.1);
    }
</style>

<div class="container mt-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 py-5 landing-content">
            <img src="assets/images/logo.png" alt="CampusFix" style="max-height: 250px;" class="mb-4 rounded">
            <p class="lead mb-5 text-muted">Welcome to the CampusFix Maintenance Request System. Please login to submit and track maintenance issues across the campus.</p>

            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="auth/login.php" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill">Login</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>