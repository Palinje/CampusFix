<?php
require_once '../includes/auth.php'; // Guard
require_once '../config/db.php';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch the user's requests ordered by newest first
$stmt = $pdo->prepare("SELECT r.*,
                                                            (SELECT COUNT(*)
                                                             FROM maintenance_requests matching_r
                                                             WHERE matching_r.location = r.location
                                                                 AND matching_r.problem_type = r.problem_type) AS request_count
                                             FROM maintenance_requests r
                                             WHERE r.user_id = ?
                                             ORDER BY r.created_at DESC");
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
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Location</th>
                            <th>Problem Type</th>
                            <th>Priority</th>
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
                                <?php
                                    $requestCount = (int) $request['request_count'];
                                    $priority = $requestCount <= 4 ? 'Low' : ($requestCount <= 8 ? 'Medium' : ($requestCount <= 12 ? 'High' : 'Extreme'));
                                    $priorityClass = $priority === 'Low' ? 'bg-success' : ($priority === 'Medium' ? 'bg-info text-dark' : ($priority === 'High' ? 'bg-warning text-dark' : 'bg-danger'));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($request['location'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($request['problem_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><span class="badge rounded-pill <?= $priorityClass ?> px-3 py-2"><?= $priority ?></span></td>
                                    <td>
                                        <?php if ($request['status'] === 'Pending'): ?>
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fa-regular fa-clock me-1"></i>Pending</span>
                                        <?php elseif ($request['status'] === 'In Progress'): ?>
                                            <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fa-solid fa-spinner fa-spin me-1"></i>In Progress</span>
                                        <?php elseif ($request['status'] === 'Completed'): ?>
                                            <span class="badge rounded-pill bg-success px-3 py-2"><i class="fa-solid fa-check me-1"></i>Completed</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary px-3 py-2"><?= htmlspecialchars($request['status'], ENT_QUOTES, 'UTF-8') ?></span>
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
