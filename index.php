<?php
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

<div class="container mt-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 py-5">
            <h1 class="display-4 fw-bold mb-4 text-primary"><i class="fa-solid fa-school me-3"></i>CampusFix</h1>
            <p class="lead mb-5 text-muted">Welcome to the CampusFix Maintenance Request System. Please login or register to submit and track maintenance issues across the campus.</p>
            
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="auth/login.php" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill">Login</a>
                <a href="auth/register.php" class="btn btn-outline-primary btn-lg px-5 shadow-sm rounded-pill">Register</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
