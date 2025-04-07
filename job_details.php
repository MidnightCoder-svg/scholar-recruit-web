
<?php
require_once 'config.php';

// Check if job ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = intval($_GET['id']);

// Get job details
$sql = "SELECT j.*, u.name as company_name, u.description as company_description, 
                u.location as company_location, u.website as company_website 
         FROM jobs j 
         JOIN users u ON j.company_id = u.id 
         WHERE j.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Job not found
    header("Location: jobs.php");
    exit;
}

$job = $result->fetch_assoc();
$page_title = $job['title'];
include 'includes/header.php';

// Handle job application
$application_message = '';
$application_type = '';

if (isset($_POST['apply']) && isLoggedIn() && checkRole(ROLE_STUDENT)) {
    $student_id = getCurrentUser()['id'];
    
    // Check if already applied
    $check_sql = "SELECT * FROM applications WHERE job_id = ? AND student_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $job_id, $student_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $application_message = "You have already applied for this job.";
        $application_type = "warning";
    } else {
        // Insert new application
        $apply_sql = "INSERT INTO applications (job_id, student_id) VALUES (?, ?)";
        $apply_stmt = $conn->prepare($apply_sql);
        $apply_stmt->bind_param("ii", $job_id, $student_id);
        
        if ($apply_stmt->execute()) {
            $application_message = "Your application has been submitted successfully!";
            $application_type = "success";
        } else {
            $application_message = "Sorry, there was a problem submitting your application. Please try again.";
            $application_type = "danger";
        }
    }
}
?>

<div class="container py-5">
    <?php if (!empty($application_message)): ?>
        <div class="alert alert-<?= $application_type ?> alert-dismissible fade show" role="alert">
            <?= $application_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Job Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title mb-2"><?= htmlspecialchars($job['title']) ?></h1>
                    <h5 class="text-muted mb-4"><?= htmlspecialchars($job['company_name']) ?></h5>
                    
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <span class="badge bg-primary p-2"><?= htmlspecialchars($job['type']) ?></span>
                        <div class="text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?>
                        </div>
                        <?php if (!empty($job['salary'])): ?>
                            <div class="text-muted">
                                <i class="bi bi-cash"></i> <?= htmlspecialchars($job['salary']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-muted">
                            <i class="bi bi-calendar"></i> Apply by: <?= htmlspecialchars($job['deadline']) ?>
                        </div>
                    </div>
                    
                    <h5>Job Description</h5>
                    <div class="mb-4">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                    
                    <?php if (!empty($job['qualifications'])): ?>
                        <h5>Qualifications</h5>
                        <div class="mb-4">
                            <?= nl2br(htmlspecialchars($job['qualifications'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($job['skills'])): ?>
                        <h5>Skills Required</h5>
                        <div class="mb-4 d-flex flex-wrap gap-1">
                            <?php foreach (explode(',', $job['skills']) as $skill): ?>
                                <span class="badge bg-secondary p-2"><?= htmlspecialchars(trim($skill)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                        <?php if (isLoggedIn() && checkRole(ROLE_STUDENT)): ?>
                            <form method="post" action="">
                                <button type="submit" name="apply" class="btn btn-primary">Apply Now</button>
                            </form>
                        <?php elseif (isLoggedIn() && !checkRole(ROLE_STUDENT)): ?>
                            <div class="alert alert-info">Only students can apply for jobs.</div>
                        <?php else: ?>
                            <a href="login.php?redirect=job_details.php?id=<?= $job_id ?>" class="btn btn-primary">Login to Apply</a>
                            <a href="register.php" class="btn btn-outline-primary">Register as Student</a>
                        <?php endif; ?>
                        <a href="jobs.php" class="btn btn-outline-secondary">Back to Jobs</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Company Info -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">About the Company</h5>
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($job['company_name']) ?></h5>
                    
                    <?php if (!empty($job['company_location'])): ?>
                        <div class="mb-2 text-muted">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['company_location']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($job['company_website'])): ?>
                        <div class="mb-3">
                            <a href="<?= htmlspecialchars($job['company_website']) ?>" target="_blank" class="text-decoration-none">
                                <i class="bi bi-globe"></i> Visit Website
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($job['company_description'])): ?>
                        <p class="card-text">
                            <?= nl2br(htmlspecialchars($job['company_description'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Job Details Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Job Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Job Type</span>
                            <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($job['type']) ?></span>
                        </li>
                        <?php if (!empty($job['duration'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Duration</span>
                                <span><?= htmlspecialchars($job['duration']) ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Posted Date</span>
                            <span><?= date('M d, Y', strtotime($job['posted_date'])) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Application Deadline</span>
                            <span><?= date('M d, Y', strtotime($job['deadline'])) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
