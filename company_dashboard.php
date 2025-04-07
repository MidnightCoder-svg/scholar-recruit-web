
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

// Get company's job postings
$jobs_sql = "SELECT j.*, 
             (SELECT COUNT(*) FROM applications WHERE job_id = j.id) AS applicant_count 
             FROM jobs j 
             WHERE j.company_id = ? 
             ORDER BY j.posted_date DESC";
$jobs_stmt = $conn->prepare($jobs_sql);
$jobs_stmt->bind_param("i", $user['id']);
$jobs_stmt->execute();
$jobs_result = $jobs_stmt->get_result();
$active_jobs_count = 0;

$jobs = [];
while ($job = $jobs_result->fetch_assoc()) {
    $jobs[] = $job;
    if (strtotime($job['deadline']) >= time()) {
        $active_jobs_count++;
    }
}

// Get applications for company's jobs
$applications_sql = "SELECT a.id, a.status, a.applied_date, j.title, j.id as job_id, 
                     u.name as student_name, u.education 
                     FROM applications a 
                     JOIN jobs j ON a.job_id = j.id 
                     JOIN users u ON a.student_id = u.id 
                     WHERE j.company_id = ? 
                     ORDER BY a.applied_date DESC";
$applications_stmt = $conn->prepare($applications_sql);
$applications_stmt->bind_param("i", $user['id']);
$applications_stmt->execute();
$applications_result = $applications_stmt->get_result();
$applications = [];
$total_applicants = 0;
$scheduled_interviews = 0;

while ($application = $applications_result->fetch_assoc()) {
    $applications[] = $application;
    $total_applicants++;
    
    if ($application['status'] === 'Interview Scheduled') {
        $scheduled_interviews++;
    }
}
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($user['photo_url'])): ?>
                        <img src="<?= htmlspecialchars($user['photo_url']) ?>" alt="Company Logo" class="img-fluid mb-3" style="max-height: 120px;">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                            <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <a href="company_profile.php" class="btn btn-outline-primary btn-sm w-100">Edit Profile</a>
                </div>
            </div>
            
            <div class="list-group mb-4">
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
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Dashboard</h3>
                <a href="post_job.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Post New Job
                </a>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="bi bi-briefcase text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Active Job Postings</h6>
                                <h4 class="mb-0"><?= $active_jobs_count ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="bi bi-people text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Applicants</h6>
                                <h4 class="mb-0"><?= $total_applicants ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="bi bi-calendar-check text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Scheduled Interviews</h6>
                                <h4 class="mb-0"><?= $scheduled_interviews ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="postings-tab" data-bs-toggle="tab" data-bs-target="#postings" type="button" role="tab">Job Postings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">Applications</button>
                </li>
            </ul>
            
            <div class="tab-content" id="dashboardTabsContent">
                <!-- Job Postings Tab -->
                <div class="tab-pane fade show active" id="postings" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Your Job Postings</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($jobs) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
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
                                            <?php foreach ($jobs as $job): ?>
                                                <?php 
                                                $is_active = strtotime($job['deadline']) >= time();
                                                $status_class = $is_active ? 'bg-success' : 'bg-secondary';
                                                $status_text = $is_active ? 'Active' : 'Expired';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($job['title']) ?></td>
                                                    <td><span class="badge bg-light text-dark"><?= htmlspecialchars($job['type']) ?></span></td>
                                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($job['posted_date'])) ?></td>
                                                    <td><?= date('M j, Y', strtotime($job['deadline'])) ?></td>
                                                    <td><span class="badge bg-primary rounded-pill"><?= $job['applicant_count'] ?></span></td>
                                                    <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary">View</a>
                                                            <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-outline-secondary">Edit</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-briefcase text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5>No job postings yet</h5>
                                    <p class="text-muted">You haven't posted any jobs or internships yet.</p>
                                    <a href="post_job.php" class="btn btn-primary mt-2">Post a Job</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Applications Tab -->
                <div class="tab-pane fade" id="applications" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Applications Received</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($applications) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Job Position</th>
                                                <th>Applied On</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($applications as $application): ?>
                                                <?php 
                                                $status_class = 
                                                    $application['status'] === 'pending' ? 'bg-secondary' : 
                                                    ($application['status'] === 'reviewed' ? 'bg-info' : 
                                                    ($application['status'] === 'Interview Scheduled' ? 'bg-primary' : 
                                                    ($application['status'] === 'rejected' ? 'bg-danger' : 'bg-success')));
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($application['student_name']) ?></td>
                                                    <td><?= htmlspecialchars($application['title']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($application['applied_date'])) ?></td>
                                                    <td><span class="badge <?= $status_class ?>"><?= htmlspecialchars(ucfirst($application['status'])) ?></span></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="student_profile.php?id=<?= $application['id'] ?>" class="btn btn-outline-primary">View Profile</a>
                                                            <a href="application_details.php?id=<?= $application['id'] ?>" class="btn btn-outline-secondary">Details</a>
                                                            <button type="button" class="btn btn-outline-success" onclick="updateApplicationStatus(<?= $application['id'] ?>, 'Interview Scheduled')">Approve</button>
                                                            <button type="button" class="btn btn-outline-danger" onclick="updateApplicationStatus(<?= $application['id'] ?>, 'rejected')">Reject</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-people text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5>No applications yet</h5>
                                    <p class="text-muted">You haven't received any applications for your job postings yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to update application status (would be AJAX in a real implementation)
    function updateApplicationStatus(applicationId, status) {
        // In a real implementation, this would send an AJAX request to update the status
        alert('Application status would be updated to: ' + status);
        
        // Reload the page after the update
        // location.reload();
    }
</script>

<?php include 'includes/footer.php'; ?>
