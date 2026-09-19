<?php
require_once '../includes/auth.php'; // Guard
require_once '../config/db.php';
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Get aggregate counts of user requests
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM maintenance_requests WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

$total = $stats['total'] ?? 0;
$pending = $stats['pending'] ?? 0;
$in_progress = $stats['in_progress'] ?? 0;
$completed = $stats['completed'] ?? 0;

// Get latest 5 requests for quick preview
$stmt = $pdo->prepare("SELECT * FROM maintenance_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$latest_requests = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-1">Welcome, <?= htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') ?>!</h2>
            <p class="text-muted mb-0 fs-5">Here is an overview of your submitted maintenance requests.</p>
        </div>
        <div class="col-auto">
            <a href="submit_request.php" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
                <i class="fa-solid fa-plus me-2"></i>New Request
            </a>
        </div>
    </div>

    <!-- Overview Metric Cards -->
    <div class="row mb-5 g-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100 rounded-4">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-bold mb-2"><?= $total ?></h1>
                    <p class="fs-5 mb-0 opacity-75">Total Submitted</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100 rounded-4">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-bold mb-2"><?= $pending ?></h1>
                    <p class="fs-5 mb-0 opacity-75">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-dark h-100 rounded-4">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-bold mb-2"><?= $in_progress ?></h1>
                    <p class="fs-5 mb-0 opacity-75">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100 rounded-4">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <h1 class="display-3 fw-bold mb-2"><?= $completed ?></h1>
                    <p class="fs-5 mb-0 opacity-75">Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 fw-bold">Recent Requests</h4>
                <a href="my_requests.php" class="text-decoration-none fw-bold">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">ID</th>
                                    <th class="py-3">Location</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 pe-4">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latest_requests)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No recent requests found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($latest_requests as $req): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">#<?= htmlspecialchars($req['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($req['location'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($req['problem_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if ($req['status'] === 'Pending'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                                <?php elseif ($req['status'] === 'In Progress'): ?>
                                                    <span class="badge bg-info text-dark rounded-pill px-3 py-2">In Progress</span>
                                                <?php elseif ($req['status'] === 'Completed'): ?>
                                                    <span class="badge bg-success rounded-pill px-3 py-2">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?= htmlspecialchars($req['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4 text-muted"><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
