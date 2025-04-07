
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
$success_message = '';
$error_message = '';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    
    // Verify that the job belongs to this company
    $verify_sql = "SELECT id FROM jobs WHERE id = ? AND company_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $job_id, $user['id']);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();
    
    if ($result->num_rows == 1) {
        // Delete job
        $delete_sql = "DELETE FROM jobs WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $job_id);
        
        if ($delete_stmt->execute()) {
            $success_message = 'Job deleted successfully!';
        } else {
            $error_message = 'Error deleting job: ' . $conn->error;
        }
    } else {
        $error_message = 'Job not found or you do not have permission to delete it.';
    }
}

// Get company's job postings with additional info
$sql = "SELECT j.*, 
         (SELECT COUNT(*) FROM applications WHERE job_id = j.id) AS applicant_count 
         FROM jobs j 
         WHERE j.company_id = ? 
         ORDER BY j.posted_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
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
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Your Job Postings</h3>
                <a href="post_job.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Post New Job
                </a>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Posted</th>
                                        <th>Deadline</th>
                                        <th>Applicants</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = $result->fetch_assoc()): ?>
                                        <?php 
                                        $is_active = strtotime($job['deadline']) >= time();
                                        $status_class = $is_active ? 'bg-success' : 'bg-secondary';
                                        $status_text = $is_active ? 'Active' : 'Expired';
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($job['type']) ?></span></td>
                                            <td><?= htmlspecialchars($job['location']) ?></td>
                                            <td><?= date('M j, Y', strtotime($job['posted_date'])) ?></td>
                                            <td><?= date('M j, Y', strtotime($job['deadline'])) ?></td>
                                            <td>
                                                <?php if ($job['applicant_count'] > 0): ?>
                                                    <a href="view_applications.php?job_id=<?= $job['id'] ?>" class="badge bg-primary text-decoration-none">
                                                        <?= $job['applicant_count'] ?> applicant<?= $job['applicant_count'] > 1 ? 's' : '' ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">0 applicants</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="job_details.php?id=<?= $job['id'] ?>">
                                                                <i class="bi bi-eye me-2"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="edit_job.php?id=<?= $job['id'] ?>">
                                                                <i class="bi bi-pencil me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="view_applications.php?job_id=<?= $job['id'] ?>">
                                                                <i class="bi bi-people me-2"></i> View Applicants
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);" 
                                                               onclick="confirmDelete(<?= $job['id'] ?>, '<?= htmlspecialchars(addslashes($job['title'])) ?>')">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-briefcase text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4>No Job Postings Yet</h4>
                        <p class="text-muted mb-4">You haven't posted any jobs or internships yet.</p>
                        <a href="post_job.php" class="btn btn-primary">Post Your First Job</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the job posting: <span id="jobTitleToDelete"></span>?</p>
                <p class="text-danger">This action cannot be undone. All applications for this job will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(jobId, jobTitle) {
        document.getElementById('jobTitleToDelete').textContent = jobTitle;
        document.getElementById('confirmDeleteBtn').href = 'manage_jobs.php?action=delete&id=' + jobId;
        
        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('deleteJobModal'));
        modal.show();
    }
</script>

<?php include 'includes/footer.php'; ?>
