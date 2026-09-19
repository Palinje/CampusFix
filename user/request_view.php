<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$id) {
    header("Location: my_requests.php");
    exit();
}

// Fetch request details, explicitly restricting to the logged-in user (IDOR protection)
$stmt = $pdo->prepare("SELECT * FROM maintenance_requests WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$request = $stmt->fetch();

if (!$request) {
    $_SESSION['flash_error'] = "Request not found or you do not have permission to view it.";
    echo "<div class='container mt-5'><div class='alert alert-danger'>Request not found or you do not have permission to view it.</div></div>";
    require_once '../includes/footer.php';
    exit();
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-ticket me-2"></i>View Request #<?= htmlspecialchars($request['id'], ENT_QUOTES, 'UTF-8') ?></h2>
        <a href="my_requests.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">&larr; Back to My Requests</a>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Ticket Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-light border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-muted mb-0">Request Details</h5>
                    <?php if ($request['status'] === 'Pending'): ?>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                    <?php elseif ($request['status'] === 'In Progress'): ?>
                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">In Progress</span>
                    <?php elseif ($request['status'] === 'Completed'): ?>
                        <span class="badge bg-success rounded-pill px-3 py-2">Completed</span>
                    <?php else: ?>
                        <span class="badge bg-secondary rounded-pill px-3 py-2"><?= htmlspecialchars($request['status'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless mb-4">
                        <tbody>
                            <tr>
                                <th class="text-muted ps-0" style="width: 150px;">Location:</th>
                                <td><span class="fw-bold fs-5 text-primary"><?= htmlspecialchars($request['location'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">Problem Type:</th>
                                <td><span class="badge bg-secondary px-3 py-2"><?= htmlspecialchars($request['problem_type'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">Description:</th>
                                <td>
                                    <div class="p-3 bg-light rounded-3 border border-light-subtle text-dark" style="min-height: 100px;">
                                        <?= nl2br(htmlspecialchars($request['description'], ENT_QUOTES, 'UTF-8')) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">Submitted On:</th>
                                <td><?= date('F j, Y, g:i a', strtotime($request['created_at'])) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if ($request['status'] === 'Pending'): ?>
                        <hr>
                        <div class="d-flex justify-content-end mt-3">
                            <form method="POST" action="../actions/delete_request.php" onsubmit="return confirm('Are you sure you want to delete this request? This action cannot be undone.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($request['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-danger shadow-sm"><i class="fa-solid fa-trash me-2"></i>Delete Request</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
