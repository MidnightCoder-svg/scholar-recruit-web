
<?php
require_once 'config.php';

// Check if user is logged in and is a company
if (!isLoggedIn() || !checkRole(ROLE_COMPANY)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();

// Check if job ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_jobs.php");
    exit;
}

$job_id = intval($_GET['id']);

// Verify that the job belongs to this company
$verify_sql = "SELECT * FROM jobs WHERE id = ? AND company_id = ?";
$verify_stmt = $conn->prepare($verify_sql);
$verify_stmt->bind_param("ii", $job_id, $user['id']);
$verify_stmt->execute();
$result = $verify_stmt->get_result();

if ($result->num_rows == 0) {
    // Job not found or doesn't belong to this company
    header("Location: manage_jobs.php");
    exit;
}

$job = $result->fetch_assoc();
$page_title = "Edit Job";
include 'includes/header.php';

// Handle job update
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
        // Update job in database
        $sql = "UPDATE jobs SET title = ?, description = ?, location = ?, type = ?, 
                salary = ?, deadline = ?, qualifications = ?, skills = ?, duration = ? 
                WHERE id = ? AND company_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssii", $title, $description, $location, $type, 
                         $salary, $deadline, $qualifications, $skills, $duration, $job_id, $user['id']);
        
        if ($stmt->execute()) {
            $success_message = 'Job updated successfully!';
            // Refresh job data
            $verify_stmt->execute();
            $result = $verify_stmt->get_result();
            $job = $result->fetch_assoc();
        } else {
            $error_message = 'Error updating job: ' . $conn->error;
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
            
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Job</h5>
                    <a href="manage_jobs.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Jobs
                    </a>
                </div>
                <div class="card-body">
                    <form action="edit_job.php?id=<?= $job_id ?>" method="post">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type" class="form-label">Job Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="Full-time" <?= $job['type'] == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                    <option value="Part-time" <?= $job['type'] == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                    <option value="Internship" <?= $job['type'] == 'Internship' ? 'selected' : '' ?>>Internship</option>
                                    <option value="Contract" <?= $job['type'] == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location *</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($job['location']) ?>" placeholder="City, State, Country or Remote" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Salary/Compensation</label>
                                <input type="text" class="form-control" id="salary" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" placeholder="e.g. $50,000 - $70,000 per year">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration" value="<?= htmlspecialchars($job['duration']) ?>" placeholder="e.g. 3 months, 1 year, Permanent">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="deadline" class="form-label">Application Deadline *</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" value="<?= htmlspecialchars($job['deadline']) ?>" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="description" class="form-label">Job Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($job['description']) ?></textarea>
                                <div class="form-text">Include responsibilities, expectations, and other relevant details.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="qualifications" class="form-label">Qualifications</label>
                                <textarea class="form-control" id="qualifications" name="qualifications" rows="4"><?= htmlspecialchars($job['qualifications']) ?></textarea>
                                <div class="form-text">List required education, certifications, or experience.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="skills" class="form-label">Required Skills</label>
                                <textarea class="form-control" id="skills" name="skills" rows="2"><?= htmlspecialchars($job['skills']) ?></textarea>
                                <div class="form-text">Separate skills with commas (e.g. JavaScript, PHP, Communication)</div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Job</button>
                                <a href="job_details.php?id=<?= $job_id ?>" class="btn btn-outline-secondary">View Job</a>
                                <a href="manage_jobs.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
