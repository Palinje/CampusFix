<?php
require_once '../includes/auth.php'; // Guard
require_once '../config/db.php';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch the user's requests ordered by newest first
$stmt = $pdo->prepare("SELECT * FROM maintenance_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fa-solid fa-list-check me-2"></i>My Requests</h2>
        <a href="submit_request.php" class="btn btn-primary shadow-sm"><i class="fa-solid fa-plus me-2"></i>New Request</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Location</th>
                            <th>Problem Type</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th class="pe-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 mb-3 d-block text-black-50"></i>
                                    You haven't submitted any maintenance requests yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">#<?= htmlspecialchars($request['id']) ?></td>
                                    <td><?= htmlspecialchars($request['location']) ?></td>
                                    <td><?= htmlspecialchars($request['problem_type']) ?></td>
                                    <td>
                                        <?php if ($request['status'] === 'Pending'): ?>
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fa-regular fa-clock me-1"></i>Pending</span>
                                        <?php elseif ($request['status'] === 'In Progress'): ?>
                                            <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fa-solid fa-spinner fa-spin me-1"></i>In Progress</span>
                                        <?php elseif ($request['status'] === 'Completed'): ?>
                                            <span class="badge rounded-pill bg-success px-3 py-2"><i class="fa-solid fa-check me-1"></i>Completed</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary px-3 py-2"><?= htmlspecialchars($request['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y h:i A', strtotime($request['created_at'])) ?></td>
                                    <td class="pe-3 text-end">
                                        <!-- Note: request_view.php is a placeholder for a future feature, if implemented -->
                                        <a href="request_view.php?id=<?= $request['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
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
