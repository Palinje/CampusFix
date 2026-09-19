<?php
require_once '../includes/auth.php';
requireAdmin(); // Guard
require_once '../config/db.php';
require_once '../includes/header.php';

// Search and filter parameters
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

// Build the query dynamically based on filters
$sql = "SELECT r.*, u.full_name, u.email 
        FROM maintenance_requests r 
        JOIN users u ON r.user_id = u.id 
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (r.location LIKE ? OR r.problem_type LIKE ? OR r.description LIKE ?)";
    $likeSearch = "%{$search}%";
    array_push($params, $likeSearch, $likeSearch, $likeSearch);
}

if ($status_filter && $status_filter !== 'All') {
    $sql .= " AND r.status = ?";
    array_push($params, $status_filter);
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-list-check me-2"></i>All Maintenance Requests</h2>
    </div>

    <!-- Search and Filter Form -->
    <div class="card shadow-sm border-0 mb-4 p-4 rounded-4 bg-light">
        <form method="GET" action="requests.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label fw-bold text-muted">Search (Room, Type, or Desc)</label>
                <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="e.g., Room 205, Plumbing...">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label fw-bold text-muted">Status Filter</label>
                <select class="form-select" id="status" name="status">
                    <option value="All" <?= $status_filter === 'All' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $status_filter === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $status_filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fa-solid fa-filter me-2"></i>Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Reporter</th>
                            <th class="py-3">Location</th>
                            <th class="py-3">Problem Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-magnifying-glass fs-2 mb-3 d-block text-black-50"></i>
                                    No requests found matching your criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= htmlspecialchars($req['id']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($req['full_name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($req['email']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($req['location']) ?></td>
                                    <td><?= htmlspecialchars($req['problem_type']) ?></td>
                                    <td>
                                        <?php if ($req['status'] === 'Pending'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                                        <?php elseif ($req['status'] === 'In Progress'): ?>
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-2">In Progress</span>
                                        <?php elseif ($req['status'] === 'Completed'): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2"><?= htmlspecialchars($req['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                                    <td class="pe-4 text-end">
                                        <a href="request_view.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-primary shadow-sm px-3">Manage</a>
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
