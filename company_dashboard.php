
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Company Dashboard";
include 'includes/header.php';

// Get company's posted jobs
$jobs_sql = "SELECT * FROM jobs WHERE company_id = ? ORDER BY posted_date DESC";
$jobs_stmt = $conn->prepare($jobs_sql);
$jobs_stmt->bind_param("i", $user['id']);
$jobs_stmt->execute();
$jobs = $jobs_stmt->get_result();

// Get applications for company's jobs
$apps_sql = "SELECT a.*, j.title as job_title, u.name as student_name, u.email as student_email, 
             u.photo_url as student_photo, u.skills as student_skills 
             FROM applications a 
             JOIN jobs j ON a.job_id = j.id 
             JOIN users u ON a.student_id = u.id 
             WHERE j.company_id = ? 
             ORDER BY a.applied_date DESC 
             LIMIT 10";
$apps_stmt = $conn->prepare($apps_sql);
$apps_stmt->bind_param("i", $user['id']);
$apps_stmt->execute();
$applications = $apps_stmt->get_result();

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
            // Refresh the page to show updated data
            header("Location: company_dashboard.php");
            exit;
        } else {
            showMessage("Error updating application status.", "error");
        }
    } else {
        showMessage("You don't have permission to update this application.", "error");
    }
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
                    <a href="company_dashboard.php" class="list-group-item list-group-item-action active">
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
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="row mb-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
                            <p class="card-text">Manage your job postings and applicants from your dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Active Jobs</h5>
                                <?php
                                $active_jobs = 0;
                                foreach ($jobs as $job) {
                                    if (strtotime($job['deadline']) >= time()) $active_jobs++;
                                }
                                $jobs->data_seek(0); // Reset result pointer
                                ?>
                                <p class="card-text h2 mb-0"><?= $active_jobs ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Total Applicants</h5>
                                <?php
                                $total_apps_sql = "SELECT COUNT(*) as count FROM applications a 
                                                  JOIN jobs j ON a.job_id = j.id 
                                                  WHERE j.company_id = ?";
                                $total_apps_stmt = $conn->prepare($total_apps_sql);
                                $total_apps_stmt->bind_param("i", $user['id']);
                                $total_apps_stmt->execute();
                                $total_apps_result = $total_apps_stmt->get_result()->fetch_assoc();
                                ?>
                                <p class="card-text h2 mb-0"><?= $total_apps_result['count'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Expiring Soon</h5>
                                <?php
                                $soon_jobs = 0;
                                $now = time();
                                $one_week_later = strtotime('+1 week');
                                foreach ($jobs as $job) {
                                    $deadline = strtotime($job['deadline']);
                                    if ($deadline >= $now && $deadline <= $one_week_later) $soon_jobs++;
                                }
                                $jobs->data_seek(0); // Reset result pointer
                                ?>
                                <p class="card-text h2 mb-0"><?= $soon_jobs ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Applications</h5>
                    <a href="applications.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if ($applications->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Job Position</th>
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
                                            <td><?= htmlspecialchars($app['job_title']) ?></td>
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
                            No applications received yet. Once students apply to your jobs, they will appear here.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Job Listings -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Job Postings</h5>
                    <a href="post_job.php" class="btn btn-sm btn-primary">Post New Job</a>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($job = $jobs->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($job['title']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span></td>
                                            <td><?= htmlspecialchars($job['location']) ?></td>
                                            <td><?= date('M d, Y', strtotime($job['posted_date'])) ?></td>
                                            <td>
                                                <?php 
                                                $deadline = strtotime($job['deadline']);
                                                $now = time();
                                                $deadline_class = 'text-success';
                                                
                                                if ($deadline < $now) {
                                                    $deadline_class = 'text-danger fw-bold';
                                                } elseif ($deadline <= strtotime('+1 week')) {
                                                    $deadline_class = 'text-warning fw-bold';
                                                }
                                                ?>
                                                <span class="<?= $deadline_class ?>">
                                                    <?= date('M d, Y', $deadline) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary">View</a>
                                                    <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-outline-secondary">Edit</a>
                                                    <a href="applications.php?job_id=<?= $job['id'] ?>" class="btn btn-outline-info">Applicants</a>
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
