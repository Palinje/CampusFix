<?php
require_once '../includes/auth.php'; // Guard
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-wrench me-2"></i>Submit Maintenance Request</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    
                    <form action="../actions/submit_request.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        
                        <div class="mb-3">
                            <label for="location" class="form-label fw-bold">Location (e.g., Room 205, Main Hall)</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="problem_type" class="form-label fw-bold">Problem Type</label>
                            <select class="form-select" id="problem_type" name="problem_type" required>
                                <option value="" disabled selected>Select a problem type...</option>
                                <option value="Plumbing">Plumbing (e.g., Leaking pipe, Broken toilet)</option>
                                <option value="Electrical">Electrical (e.g., Broken light, Outlets not working)</option>
                                <option value="Furniture">Furniture (e.g., Broken chair, Wobbly desk)</option>
                                <option value="IT/Equipment">IT/Equipment (e.g., Projector broken, AC not working)</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" placeholder="Please provide specific details about the issue to help our maintenance team..." required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
