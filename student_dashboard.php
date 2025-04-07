
<?php
require_once 'config.php';

// Check if user is logged in and is a student
if (!isLoggedIn() || !checkRole(ROLE_STUDENT)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Student Dashboard";
include 'includes/header.php';

// Get user's applications
$sql = "SELECT a.*, j.title as job_title, j.deadline, j.type, 
        u.name as company_name 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN users u ON j.company_id = u.id 
        WHERE a.student_id = ? 
        ORDER BY a.applied_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$applications = $stmt->get_result();

// Get recommended jobs based on skills
$recommended_jobs = [];
if (!empty($user['skills'])) {
    $skills_array = explode(',', $user['skills']);
    $placeholders = implode(',', array_fill(0, count($skills_array), '?'));
    
    $skills_sql = "SELECT j.*, u.name as company_name 
                   FROM jobs j 
                   JOIN users u ON j.company_id = u.id 
                   WHERE j.deadline >= CURRENT_DATE 
                   AND (";
    
    $skills_conditions = [];
    foreach ($skills_array as $skill) {
        $skills_conditions[] = "j.skills LIKE ?";
    }
    
    $skills_sql .= implode(" OR ", $skills_conditions) . ") 
                   AND j.id NOT IN (
                       SELECT job_id FROM applications WHERE student_id = ?
                   )
                   ORDER BY j.posted_date DESC 
                   LIMIT 5";
    
    $skills_stmt = $conn->prepare($skills_sql);
    
    $bind_params = [];
    $types = "";
    
    foreach ($skills_array as $skill) {
        $bind_params[] = "%".trim($skill)."%";
        $types .= "s";
    }
    
    $bind_params[] = $user['id'];
    $types .= "i";
    
    $skills_stmt->bind_param($types, ...$bind_params);
    $skills_stmt->execute();
    $recommended_jobs = $skills_stmt->get_result();
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
                    <a href="student_dashboard.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="profile.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                    <a href="jobs.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-search me-2"></i> Browse Jobs
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
                            <h2 class="card-title">Welcome back, <?= htmlspecialchars($user['name']) ?>!</h2>
                            <p class="card-text">Here's what's happening with your job applications and career opportunities.</p>
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
                                <h5 class="card-title">Applications</h5>
                                <p class="card-text h2 mb-0"><?= $applications->num_rows ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Accepted</h5>
                                <?php
                                $accepted_count = 0;
                                foreach ($applications as $app) {
                                    if ($app['status'] == 'accepted') $accepted_count++;
                                }
                                $applications->data_seek(0);
                                ?>
                                <p class="card-text h2 mb-0"><?= $accepted_count ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Pending</h5>
                                <?php
                                $pending_count = 0;
                                foreach ($applications as $app) {
                                    if ($app['status'] == 'pending') $pending_count++;
                                }
                                $applications->data_seek(0);
                                ?>
                                <p class="card-text h2 mb-0"><?= $pending_count ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Applications</h5>
                </div>
                <div class="card-body">
                    <?php if ($applications->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Company</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = $applications->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($app['job_title']) ?></td>
                                            <td><?= htmlspecialchars($app['company_name']) ?></td>
                                            <td><?= date('M d, Y', strtotime($app['applied_date'])) ?></td>
                                            <td>
                                                <?php 
                                                $status_class = 'bg-secondary';
                                                switch ($app['status']) {
                                                    case 'accepted':
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 'rejected':
                                                        $status_class = 'bg-danger';
                                                        break;
                                                    case 'reviewed':
                                                        $status_class = 'bg-info';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'bg-warning text-dark';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $status_class ?>">
                                                    <?= ucfirst(htmlspecialchars($app['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="job_details.php?id=<?= $app['job_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    View Job
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't applied to any jobs yet. <a href="jobs.php" class="alert-link">Browse available jobs</a> to get started.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recommended Jobs -->
            <?php if ($recommended_jobs && $recommended_jobs->num_rows > 0): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Recommended Jobs Based on Your Skills</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php while ($job = $recommended_jobs->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($job['title']) ?>
                                                </a>
                                            </h5>
                                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($job['company_name']) ?></h6>
                                            
                                            <div class="mb-2 d-flex gap-2">
                                                <span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($job['location']) ?></span>
                                            </div>
                                            
                                            <p class="card-text small">
                                                <?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...
                                            </p>
                                        </div>
                                        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Deadline: <?= date('M d, Y', strtotime($job['deadline'])) ?></small>
                                            <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
