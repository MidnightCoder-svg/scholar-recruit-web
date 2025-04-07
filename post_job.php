
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Post a Job";
include 'includes/header.php';

// Handle job submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    $type = sanitizeInput($_POST['type']);
    $salary = sanitizeInput($_POST['salary']);
    $deadline = sanitizeInput($_POST['deadline']);
    $qualifications = sanitizeInput($_POST['qualifications']);
    $skills = sanitizeInput($_POST['skills']);
    $duration = sanitizeInput($_POST['duration']);
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($location) || empty($type) || empty($deadline)) {
        $error_message = 'Please fill out all required fields.';
    } else {
        // Insert job into database
        $sql = "INSERT INTO jobs (company_id, title, description, location, type, salary, 
                               deadline, qualifications, skills, duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssss", $user['id'], $title, $description, $location, 
                         $type, $salary, $deadline, $qualifications, $skills, $duration);
        
        if ($stmt->execute()) {
            $job_id = $stmt->insert_id;
            $success_message = 'Job posted successfully!';
            
            // Redirect to the job details page after a short delay
            header("refresh:2;url=job_details.php?id=$job_id");
        } else {
            $error_message = 'Error posting job: ' . $conn->error;
        }
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
                    <a href="post_job.php" class="list-group-item list-group-item-action active">
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
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Post a New Job Opportunity</h5>
                </div>
                <div class="card-body">
                    <form action="post_job.php" method="post">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type" class="form-label">Job Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select job type</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Internship">Internship</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location *</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="City, State, Country or Remote" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Salary/Compensation</label>
                                <input type="text" class="form-control" id="salary" name="salary" placeholder="e.g. $50,000 - $70,000 per year">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g. 3 months, 1 year, Permanent">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="deadline" class="form-label">Application Deadline *</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="description" class="form-label">Job Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                <div class="form-text">Include responsibilities, expectations, and other relevant details.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="qualifications" class="form-label">Qualifications</label>
                                <textarea class="form-control" id="qualifications" name="qualifications" rows="4"></textarea>
                                <div class="form-text">List required education, certifications, or experience.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="skills" class="form-label">Required Skills</label>
                                <textarea class="form-control" id="skills" name="skills" rows="2"></textarea>
                                <div class="form-text">Separate skills with commas (e.g. JavaScript, PHP, Communication)</div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Post Job</button>
                                <a href="company_dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
