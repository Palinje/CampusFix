<?php
require_once '../includes/auth.php';
requireSuperAdmin(); // Guard for super admin only
require_once '../config/db.php';
require_once '../includes/header.php';

// Fetch all users
$stmt = $pdo->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-users me-2"></i>Manage Users</h2>
        <div class="d-flex gap-2">
            <a href="../auth/register.php" class="btn btn-primary shadow-sm rounded-pill px-4"><i class="fa-solid fa-user-plus me-2"></i>Register User</a>
            <a href="dashboard.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">&larr; Back to Dashboard</a>
        </div>
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

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Registered On</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No users found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?>
                                        <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-primary ms-2">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><a href="mailto:<?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></a></td>
                                    <td>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fa-solid fa-shield-halved me-1"></i>Admin</span>
                                        <?php elseif ($u['role'] === 'maintenance'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="fa-solid fa-wrench me-1"></i>Maintenance</span>
                                        <?php elseif ($u['role'] === 'staff' || $u['role'] === 'teacher'): ?>
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-2"><?= htmlspecialchars(ucfirst($u['role']), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">Student</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                    <td class="pe-4 text-end">
                                        <a href="user_view.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
