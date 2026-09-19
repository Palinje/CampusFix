<?php
require_once '../includes/auth.php';
requireAdmin(); // Guard
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: requests.php");
    exit();
}

// Fetch request details along with user info
$stmt = $pdo->prepare("SELECT r.*, u.full_name, u.email, u.role 
                       FROM maintenance_requests r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();

if (!$request) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Request not found.</div></div>";
    require_once '../includes/footer.php';
    exit();
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-ticket me-2"></i>Manage Request #<?= htmlspecialchars($request['id'], ENT_QUOTES, 'UTF-8') ?></h2>
        <a href="requests.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">&larr; Back to Requests</a>
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
        <!-- Ticket Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-light border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-muted mb-0">Request Details</h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless mb-0">
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
                </div>
            </div>
        </div>

        <!-- Sidebar: Reporter & Status Update -->
        <div class="col-lg-4">
            <!-- Update Status -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 border-top border-4 border-primary">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">Update Status</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="../actions/update_status.php">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        <div class="mb-4">
                            <label for="status" class="form-label text-muted fw-bold">Current Status</label>
                            <select class="form-select border-2" id="status" name="status" required>
                                <option value="Pending" <?= $request['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="In Progress" <?= $request['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= $request['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm py-2">Save Status</button>
                    </form>
                </div>
            </div>

            <!-- Reporter Info -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-muted mb-0">Reporter Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($request['full_name'], ENT_QUOTES, 'UTF-8') ?></h6>
                            <span class="badge bg-secondary mt-1"><?= htmlspecialchars(ucfirst($request['role'], ENT_QUOTES, 'UTF-8')) ?></span>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0 text-muted"><i class="fa-solid fa-envelope me-2 text-primary"></i><a href="mailto:<?= htmlspecialchars($request['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none"><?= htmlspecialchars($request['email'], ENT_QUOTES, 'UTF-8') ?></a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
