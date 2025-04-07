
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();

// Check if application ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$app_id = intval($_GET['id']);

// Verify that the application belongs to one of this company's jobs
$app_sql = "SELECT a.*, j.title as job_title, j.description as job_description, 
            j.type as job_type, j.location as job_location, j.deadline,
            s.id as student_id, s.name as student_name, s.email as student_email, 
            s.photo_url as student_photo, s.bio as student_bio, s.skills as student_skills,
            s.education as student_education, s.experience as student_experience,
            s.phone as student_phone, s.location as student_location, s.website as student_website
            FROM applications a 
            JOIN jobs j ON a.job_id = j.id 
            JOIN users s ON a.student_id = s.id
            WHERE a.id = ? AND j.company_id = ?";
$app_stmt = $conn->prepare($app_sql);
$app_stmt->bind_param("ii", $app_id, $user['id']);
$app_stmt->execute();
$result = $app_stmt->get_result();

if ($result->num_rows == 0) {
    // Application not found or doesn't belong to this company's job
    header("Location: applications.php");
    exit;
}

$application = $result->fetch_assoc();
$page_title = "Application Details";
include 'includes/header.php';

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['status'])) {
    $status = $_POST['status'];
    
    $update_sql = "UPDATE applications SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $app_id);
    
    if ($update_stmt->execute()) {
        showMessage("Application status updated successfully.");
        // Refresh application data
        $app_stmt->execute();
        $result = $app_stmt->get_result();
        $application = $result->fetch_assoc();
    } else {
        showMessage("Error updating application status.", "error");
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
            
            <!-- Application Status -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Application Status</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="status" class="form-label">Current Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" <?= $application['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="reviewed" <?= $application['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                <option value="accepted" <?= $application['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                <option value="rejected" <?= $application['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                    
                    <?php
                    // Show status info based on current status
                    $status_info = '';
                    $status_class = 'alert-info';
                    
                    switch($application['status']) {
                        case 'pending':
                            $status_info = 'This application is pending your review.';
                            $status_class = 'alert-warning';
                            break;
                        case 'reviewed':
                            $status_info = 'You have reviewed this application but haven\'t made a final decision.';
                            $status_class = 'alert-info';
                            break;
                        case 'accepted':
                            $status_info = 'You have accepted this candidate for the position.';
                            $status_class = 'alert-success';
                            break;
                        case 'rejected':
                            $status_info = 'You have rejected this application.';
                            $status_class = 'alert-danger';
                            break;
                    }
                    ?>
                    
                    <div class="alert <?= $status_class ?> mt-3 mb-0">
                        <?= $status_info ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="mailto:<?= htmlspecialchars($application['student_email']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-envelope me-2"></i> Email Applicant
                    </a>
                    <a href="job_details.php?id=<?= $application['job_id'] ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-briefcase me-2"></i> View Job Posting
                    </a>
                    <a href="applications.php?job_id=<?= $application['job_id'] ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> All Applicants for this Job
                    </a>
                    <a href="applications.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-arrow-left me-2"></i> Back to Applications
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Application Info -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Application Details</h5>
                    <span class="badge <?= $application['status'] == 'pending' ? 'bg-warning text-dark' : 
                                         ($application['status'] == 'reviewed' ? 'bg-info' : 
                                          ($application['status'] == 'accepted' ? 'bg-success' : 'bg-danger')) ?>">
                        <?= ucfirst(htmlspecialchars($application['status'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Job Details</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Position</dt>
                                <dd class="col-sm-8">
                                    <a href="job_details.php?id=<?= $application['job_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($application['job_title']) ?>
                                    </a>
                                </dd>
                                
                                <dt class="col-sm-4">Type</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-primary"><?= htmlspecialchars($application['job_type']) ?></span>
                                </dd>
                                
                                <dt class="col-sm-4">Location</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($application['job_location']) ?></dd>
                                
                                <dt class="col-sm-4">Deadline</dt>
                                <dd class="col-sm-8"><?= date('F d, Y', strtotime($application['deadline'])) ?></dd>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Application Info</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Applied On</dt>
                                <dd class="col-sm-8"><?= date('F d, Y', strtotime($application['applied_date'])) ?></dd>
                                
                                <dt class="col-sm-4">Time Since</dt>
                                <dd class="col-sm-8">
                                    <?php
                                    $applied_date = new DateTime($application['applied_date']);
                                    $now = new DateTime();
                                    $interval = $applied_date->diff($now);
                                    
                                    if ($interval->y > 0) {
                                        echo $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->m > 0) {
                                        echo $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->d > 0) {
                                        echo $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->h > 0) {
                                        echo $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                    } else {
                                        echo $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                    }
                                    ?>
                                </dd>
                                
                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    <span class="badge <?= $application['status'] == 'pending' ? 'bg-warning text-dark' : 
                                                        ($application['status'] == 'reviewed' ? 'bg-info' : 
                                                        ($application['status'] == 'accepted' ? 'bg-success' : 'bg-danger')) ?>">
                                        <?= ucfirst(htmlspecialchars($application['status'])) ?>
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Applicant Profile -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Applicant Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <?php if (!empty($application['student_photo'])): ?>
                                <img src="<?= htmlspecialchars($application['student_photo']) ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                    <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h4><?= htmlspecialchars($application['student_name']) ?></h4>
                            
                            <div class="mb-3">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($application['student_email']) ?>
                                </div>
                                
                                <?php if (!empty($application['student_phone'])): ?>
                                    <div class="text-muted mb-2">
                                        <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($application['student_phone']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($application['student_location'])): ?>
                                    <div class="text-muted mb-2">
                                        <i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($application['student_location']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($application['student_website'])): ?>
                                    <div class="text-muted mb-2">
                                        <i class="bi bi-globe me-2"></i>
                                        <a href="<?= htmlspecialchars($application['student_website']) ?>" target="_blank">
                                            <?= htmlspecialchars($application['student_website']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($application['student_bio'])): ?>
                                <div class="mb-3">
                                    <h6>Bio</h6>
                                    <p><?= nl2br(htmlspecialchars($application['student_bio'])) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($application['student_skills'])): ?>
                                <div class="mb-3">
                                    <h6>Skills</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach (explode(',', $application['student_skills']) as $skill): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars(trim($skill)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($application['student_education'])): ?>
                        <div class="mb-4">
                            <h6>Education</h6>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(htmlspecialchars($application['student_education'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($application['student_experience'])): ?>
                        <div class="mb-4">
                            <h6>Work Experience</h6>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(htmlspecialchars($application['student_experience'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="applications.php?job_id=<?= $application['job_id'] ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Applicants
                        </a>
                        <div>
                            <a href="mailto:<?= htmlspecialchars($application['student_email']) ?>" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i> Contact Applicant
                            </a>
                            
                            <?php if ($application['status'] == 'pending'): ?>
                                <a href="#" class="btn btn-success mark-reviewed" data-status="reviewed">
                                    <i class="bi bi-check-circle me-1"></i> Mark as Reviewed
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle "Mark as Reviewed" button click
    const reviewBtn = document.querySelector('.mark-reviewed');
    if (reviewBtn) {
        reviewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('status').value = this.dataset.status;
            document.querySelector('form').submit();
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
