<?php
require_once '../includes/auth.php';
requireAdmin(); // Guard for admin/maintenance only
require_once '../config/db.php';
require_once '../includes/header.php';

// Aggregate counts
$stmt = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM maintenance_requests");
$stats = $stmt->fetch();

$total = $stats['total'] ?? 0;
$pending = $stats['pending'] ?? 0;
$in_progress = $stats['in_progress'] ?? 0;
$completed = $stats['completed'] ?? 0;
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-gauge me-2"></i>Admin Dashboard</h2>
    </div>
    
    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center h-100 p-4 shadow-sm rounded-4 border-0">
                <h4 class="opacity-75">Total</h4>
                <h1 class="display-3 fw-bold mb-0"><?= $total ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark text-center h-100 p-4 shadow-sm rounded-4 border-0">
                <h4 class="opacity-75">Pending</h4>
                <h1 class="display-3 fw-bold mb-0"><?= $pending ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-dark text-center h-100 p-4 shadow-sm rounded-4 border-0">
                <h4 class="opacity-75">In Progress</h4>
                <h1 class="display-3 fw-bold mb-0"><?= $in_progress ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center h-100 p-4 shadow-sm rounded-4 border-0">
                <h4 class="opacity-75">Completed</h4>
                <h1 class="display-3 fw-bold mb-0"><?= $completed ?></h1>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="requests.php" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill">Manage All Requests <i class="fa-solid fa-arrow-right ms-2"></i></a>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
