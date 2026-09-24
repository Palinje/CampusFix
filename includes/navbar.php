<?php
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>
<div class="container px-0">
    <nav class="navbar navbar-expand-lg navbar-dark glass-navbar sticky-top w-100 px-3 px-lg-4">
        <div class="container-fluid px-0">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="CampusFix Logo" height="30" class="me-2 rounded">
                <span>Campus<span style="color: #1677FF;">Fix</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($user_role === 'student' || $user_role === 'teacher' || $user_role === 'staff'): ?>
                        <!-- Regular User Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/user/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/user/submit_request.php">Submit Request</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/user/my_requests.php">My Requests</a>
                        </li>
                    <?php elseif ($user_role === 'admin' || $user_role === 'maintenance'): ?>
                        <!-- Admin / Maintenance Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/admin/requests.php">All Requests</a>
                        </li>
                        <?php if ($user_role === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/admin/users.php">Manage Users</a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Guest Links -->
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">
                                <i class="fa-solid fa-house"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if ($user_role): ?>
                        <li class="nav-item">
                            <span class="nav-link text-light me-3">
                                <i class="fa-solid fa-user"></i>
                                <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger text-white px-3" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link btn glass-btn-login px-4 py-2" href="<?= BASE_URL ?>/auth/login.php">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</div>