
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Manage Jobs";
include 'includes/header.php';

// Handle job deletion
if (isset($_POST['delete_job']) && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);
    
    // Verify that the job belongs to this company
    $verify_sql = "SELECT id FROM jobs WHERE id = ? AND company_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $job_id, $user['id']);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $delete_sql = "DELETE FROM jobs WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $job_id);
        
        if ($delete_stmt->execute()) {
            showMessage("Job posting deleted successfully.");
        } else {
            showMessage("Error deleting job posting.", "error");
        }
    } else {
        showMessage("You don't have permission to delete this job posting.", "error");
    }
}

// Get company's posted jobs
$jobs_sql = "SELECT * FROM jobs WHERE company_id = ? ORDER BY posted_date DESC";
$jobs_stmt = $conn->prepare($jobs_sql);
$jobs_stmt->bind_param("i", $user['id']);
$jobs_stmt->execute();
$jobs = $jobs_stmt->get_result();

// Get application counts for each job
$job_apps = array();
if ($jobs->num_rows > 0) {
    while ($job = $jobs->fetch_assoc()) {
        $app_count_sql = "SELECT COUNT(*) as count FROM applications WHERE job_id = ?";
        $app_count_stmt = $conn->prepare($app_count_sql);
        $app_count_stmt->bind_param("i", $job['id']);
        $app_count_stmt->execute();
        $app_count_result = $app_count_stmt->get_result()->fetch_assoc();
        $job_apps[$job['id']] = $app_count_result['count'];
    }
    $jobs->data_seek(0); // Reset result pointer
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
                    <a href="manage_jobs.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-briefcase me-2"></i> Manage Jobs
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Job Postings</h5>
                    <a href="post_job.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Post New Job
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($jobs->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Posted Date</th>
                                        <th>Deadline</th>
                                        <th>Applications</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = $jobs->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none fw-bold">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span></td>
                                            <td><?= htmlspecialchars($job['location']) ?></td>
                                            <td><?= date('M d, Y', strtotime($job['posted_date'])) ?></td>
                                            <td>
                                                <?php 
                                                $deadline = strtotime($job['deadline']);
                                                $now = time();
                                                $deadline_class = 'text-success';
                                                $is_expired = false;
                                                
                                                if ($deadline < $now) {
                                                    $deadline_class = 'text-danger fw-bold';
                                                    $is_expired = true;
                                                } elseif ($deadline <= strtotime('+1 week')) {
                                                    $deadline_class = 'text-warning fw-bold';
                                                }
                                                ?>
                                                <span class="<?= $deadline_class ?>">
                                                    <?= date('M d, Y', $deadline) ?>
                                                    <?= $is_expired ? ' (Expired)' : '' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="applications.php?job_id=<?= $job['id'] ?>" class="text-decoration-none">
                                                    <?= $job_apps[$job['id']] ?? 0 ?> applications
                                                </a>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteJobModal<?= $job['id'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteJobModal<?= $job['id'] ?>" tabindex="-1" aria-labelledby="deleteJobModalLabel<?= $job['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteJobModalLabel<?= $job['id'] ?>">Confirm Deletion</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete the job "<strong><?= htmlspecialchars($job['title']) ?></strong>"?</p>
                                                                <p class="text-danger">This action cannot be undone. All applications for this job will also be deleted.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form method="post" action="">
                                                                    <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                                                    <button type="submit" name="delete_job" class="btn btn-danger">Delete Job</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't posted any jobs yet. <a href="post_job.php" class="alert-link">Post your first job</a> to start receiving applications.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
