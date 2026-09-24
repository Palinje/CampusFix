<?php
require_once '../includes/auth.php';
requireSuperAdmin(); // Guard for super admin only
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: users.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>User not found.</div></div>";
    require_once '../includes/footer.php';
    exit();
}

$is_self = ($user['id'] == $_SESSION['user_id']);

// Aggregate user request stats
$stmt_stats = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM maintenance_requests WHERE user_id = ?");
$stmt_stats->execute([$id]);
$stats = $stmt_stats->fetch();

$completedProblems = [];
if ($user['role'] === 'maintenance') {
    $stmt_completed = $pdo->prepare("SELECT location, problem_type, description
                                     FROM maintenance_requests
                                     WHERE completed_by = ? AND status = 'Completed'
                                     ORDER BY id DESC");
    $stmt_completed->execute([$id]);
    $completedProblems = $stmt_completed->fetchAll();
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-user-pen me-2"></i>Manage User #<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></h2>
        <a href="users.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">&larr; Back to Users</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- User Profile Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-light border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-muted mb-0">User Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?> 
                                <?php if ($is_self): ?>
                                    <span class="badge bg-primary fs-6 ms-2 align-middle">You</span>
                                <?php endif; ?>
                            </h3>
                            <p class="mb-0 text-muted fs-5"><i class="fa-solid fa-envelope me-2 text-primary"></i><a href="mailto:<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></a></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="fw-bold text-muted mb-3 mt-4">Platform Activity</h6>
                    <?php if ($user['role'] === 'maintenance'): ?>
                        <div class="mb-3">
                            <h6 class="fw-bold text-success">Problems Completed</h6>
                            <?php if (empty($completedProblems)): ?>
                                <p class="text-muted mb-0">No completed problems yet.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($completedProblems as $problem): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong><?= htmlspecialchars($problem['location'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <span class="badge bg-success rounded-pill">Completed</span>
                                            </div>
                                            <div class="text-primary small fw-bold"><?= htmlspecialchars($problem['problem_type'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($problem['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                    <div class="row text-center g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                <h2 class="fw-bold text-primary mb-0"><?= $stats['total'] ?? 0 ?></h2>
                                <span class="text-muted small">Total Requests</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                <h2 class="fw-bold text-warning mb-0"><?= $stats['pending'] ?? 0 ?></h2>
                                <span class="text-muted small">Pending</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                <h2 class="fw-bold text-success mb-0"><?= $stats['completed'] ?? 0 ?></h2>
                                <span class="text-muted small">Completed</span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <p class="text-muted small text-end mt-3">Registered on: <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Sidebar: Role Update & Danger Zone -->
        <div class="col-lg-4">
            <!-- Update Role -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-top border-4 border-primary">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">System Role</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="../actions/update_user_role.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                        
                        <div class="mb-4">
                            <label for="role" class="form-label text-muted fw-bold">Current Role</label>
                            <select class="form-select border-2" id="role" name="role" required <?= $is_self ? 'disabled' : '' ?>>
                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="maintenance" <?= $user['role'] === 'maintenance' ? 'selected' : '' ?>>Maintenance Worker</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                            <?php if ($is_self): ?>
                                <div class="form-text text-danger mt-2"><i class="fa-solid fa-triangle-exclamation me-1"></i>You cannot demote your own account.</div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 shadow-sm py-2" <?= $is_self ? 'disabled' : '' ?>>Save Role</button>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card shadow-sm border-0 rounded-4 mt-4 border-top border-4 border-danger">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0 text-danger">Danger Zone</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">Permanently deleting this user will also delete all of their submitted maintenance requests (cascade delete).</p>
                    <form method="POST" action="../actions/delete_user.php" onsubmit="return confirm('Are you absolutely sure you want to delete this user? This action cannot be undone and will destroy all of their data.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 shadow-sm py-2" <?= $is_self ? 'disabled' : '' ?>><i class="fa-solid fa-trash me-2"></i>Delete User</button>
                    </form>
                    <?php if ($is_self): ?>
                        <div class="form-text text-danger text-center mt-2"><i class="fa-solid fa-triangle-exclamation me-1"></i>You cannot delete your own account.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
