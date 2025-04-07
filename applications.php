
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Manage Applications";
include 'includes/header.php';

// Get job ID if provided
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : null;

// Verify job belongs to this company if job_id is provided
if ($job_id) {
    $job_sql = "SELECT * FROM jobs WHERE id = ? AND company_id = ?";
    $job_stmt = $conn->prepare($job_sql);
    $job_stmt->bind_param("ii", $job_id, $user['id']);
    $job_stmt->execute();
    $job_result = $job_stmt->get_result();
    
    if ($job_result->num_rows == 0) {
        // Job not found or doesn't belong to this company
        header("Location: company_dashboard.php");
        exit;
    }
    
    $job = $job_result->fetch_assoc();
}

// Handle application status update
if (isset($_POST['update_status']) && isset($_POST['application_id']) && isset($_POST['status'])) {
    $app_id = intval($_POST['application_id']);
    $status = $_POST['status'];
    
    // Verify that the application belongs to one of this company's jobs
    $verify_sql = "SELECT a.id FROM applications a 
                   JOIN jobs j ON a.job_id = j.id 
                   WHERE a.id = ? AND j.company_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $app_id, $user['id']);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $update_sql = "UPDATE applications SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $status, $app_id);
        
        if ($update_stmt->execute()) {
            showMessage("Application status updated successfully.");
        } else {
            showMessage("Error updating application status.", "error");
        }
    } else {
        showMessage("You don't have permission to update this application.", "error");
    }
}

// Get applications
$where_clause = "j.company_id = ?";
$params = array($user['id']);
$types = "i";

if ($job_id) {
    $where_clause .= " AND j.id = ?";
    $params[] = $job_id;
    $types .= "i";
}

// Add status filter if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
if ($status_filter && in_array($status_filter, ['pending', 'reviewed', 'accepted', 'rejected'])) {
    $where_clause .= " AND a.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$apps_sql = "SELECT a.*, j.title as job_title, j.deadline, j.type, 
             u.name as student_name, u.email as student_email, 
             u.photo_url as student_photo, u.skills as student_skills 
             FROM applications a 
             JOIN jobs j ON a.job_id = j.id 
             JOIN users u ON a.student_id = u.id 
             WHERE $where_clause 
             ORDER BY a.applied_date DESC";

$apps_stmt = $conn->prepare($apps_sql);
$apps_stmt->bind_param($types, ...$params);
$apps_stmt->execute();
$applications = $apps_stmt->get_result();

// Get application counts by status
$status_counts = array(
    'total' => 0,
    'pending' => 0,
    'reviewed' => 0,
    'accepted' => 0,
    'rejected' => 0
);

$count_sql = "SELECT a.status, COUNT(*) as count 
             FROM applications a 
             JOIN jobs j ON a.job_id = j.id 
             WHERE j.company_id = ?";
if ($job_id) {
    $count_sql .= " AND j.id = ?";
}
$count_sql .= " GROUP BY a.status";

$count_stmt = $conn->prepare($count_sql);
if ($job_id) {
    $count_stmt->bind_param("ii", $user['id'], $job_id);
} else {
    $count_stmt->bind_param("i", $user['id']);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();

while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
    $status_counts['total'] += $row['count'];
}
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Dashboard</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="company_dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="company_profile.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-building me-2"></i> Company Profile
                    </a>
                    <a href="post_job.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-plus-circle me-2"></i> Post a Job
                    </a>
                    <a href="manage_jobs.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-briefcase me-2"></i> Manage Jobs
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filter Applications</h5>
                </div>
                <div class="card-body">
                    <form action="" method="get">
                        <?php if ($job_id): ?>
                            <input type="hidden" name="job_id" value="<?= $job_id ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="reviewed" <?= $status_filter == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                <option value="accepted" <?= $status_filter == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <noscript>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </noscript>
                    </form>
                    
                    <?php if ($status_filter || $job_id): ?>
                        <div class="d-grid mt-3">
                            <a href="<?= $job_id ? "applications.php?job_id=$job_id" : 'applications.php' ?>" class="btn btn-outline-secondary">
                                Clear Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Status Counts -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Application Stats</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= $job_id ? "applications.php?job_id=$job_id" : 'applications.php' ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        All Applications
                        <span class="badge bg-primary rounded-pill"><?= $status_counts['total'] ?></span>
                    </a>
                    <a href="<?= $job_id ? "applications.php?job_id=$job_id&status=pending" : 'applications.php?status=pending' ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Pending
                        <span class="badge bg-warning text-dark rounded-pill"><?= $status_counts['pending'] ?></span>
                    </a>
                    <a href="<?= $job_id ? "applications.php?job_id=$job_id&status=reviewed" : 'applications.php?status=reviewed' ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Reviewed
                        <span class="badge bg-info rounded-pill"><?= $status_counts['reviewed'] ?></span>
                    </a>
                    <a href="<?= $job_id ? "applications.php?job_id=$job_id&status=accepted" : 'applications.php?status=accepted' ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Accepted
                        <span class="badge bg-success rounded-pill"><?= $status_counts['accepted'] ?></span>
                    </a>
                    <a href="<?= $job_id ? "applications.php?job_id=$job_id&status=rejected" : 'applications.php?status=rejected' ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Rejected
                        <span class="badge bg-danger rounded-pill"><?= $status_counts['rejected'] ?></span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <?php if ($job_id): ?>
                            Applications for "<?= htmlspecialchars($job['title']) ?>"
                        <?php else: ?>
                            All Applications
                        <?php endif; ?>
                    </h5>
                    <?php if ($job_id): ?>
                        <a href="manage_jobs.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Jobs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($applications->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <?php if (!$job_id): ?>
                                            <th>Job Position</th>
                                        <?php endif; ?>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = $applications->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($app['student_photo'])): ?>
                                                        <img src="<?= htmlspecialchars($app['student_photo']) ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($app['student_name']) ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars($app['student_email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php if (!$job_id): ?>
                                                <td>
                                                    <div>
                                                        <a href="job_details.php?id=<?= $app['job_id'] ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($app['job_title']) ?>
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-primary"><?= htmlspecialchars($app['type']) ?></span>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                            <td><?= date('M d, Y', strtotime($app['applied_date'])) ?></td>
                                            <td>
                                                <form method="post" action="" class="status-form">
                                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                                    <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()">
                                                        <option value="pending" <?= $app['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="reviewed" <?= $app['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                                        <option value="accepted" <?= $app['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                                        <option value="rejected" <?= $app['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                    </select>
                                                    <noscript>
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-outline-primary mt-1">Update</button>
                                                    </noscript>
                                                </form>
                                            </td>
                                            <td>
                                                <a href="view_application.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?php if ($status_filter): ?>
                                No applications with status "<?= ucfirst(htmlspecialchars($status_filter)) ?>" found.
                            <?php elseif ($job_id): ?>
                                No applications received for this job yet.
                            <?php else: ?>
                                No applications received yet. Once students apply to your jobs, they will appear here.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit status change forms
    const statusForms = document.querySelectorAll('.status-form');
    statusForms.forEach(form => {
        const select = form.querySelector('.status-select');
        select.addEventListener('change', function() {
            form.submit();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
