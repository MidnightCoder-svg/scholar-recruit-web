
<?php
require_once 'config.php';
$page_title = "Home";
include 'includes/header.php';

// Get recent jobs
$sql = "SELECT j.*, u.name as company_name 
        FROM jobs j 
        JOIN users u ON j.company_id = u.id 
        WHERE j.deadline >= CURRENT_DATE 
        ORDER BY j.posted_date DESC 
        LIMIT 6";
$result = $conn->query($sql);
?>

<div class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Find Your Dream Career</h1>
                <p class="lead my-4">Connect with top companies and land your ideal internship or job. Your professional journey starts here.</p>
                <div class="d-flex gap-3">
                    <a href="jobs.php" class="btn btn-light btn-lg">Browse Jobs</a>
                    <a href="register.php" class="btn btn-outline-light btn-lg">Sign Up</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="assets/images/hero-image.svg" alt="Career opportunities" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Latest Opportunities</h2>
        
        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($job = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm job-card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                                <p class="card-text text-muted"><?= htmlspecialchars($job['company_name']) ?></p>
                                <div class="d-flex gap-2 mb-3">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($job['location']) ?></span>
                                    <span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span>
                                </div>
                                <p class="card-text small">
                                    <?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...
                                </p>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                                <small class="text-muted">Apply by: <?= htmlspecialchars($job['deadline']) ?></small>
                                <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No job opportunities available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="jobs.php" class="btn btn-primary">View All Jobs</a>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="mb-3 text-primary display-5">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h4>Create Account</h4>
                    <p class="text-muted">Register as a student or company. Build your profile to showcase your talents or company.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="mb-3 text-primary display-5">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4>Find Opportunities</h4>
                    <p class="text-muted">Browse through internships and job postings from top companies.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="mb-3 text-primary display-5">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h4>Apply & Connect</h4>
                    <p class="text-muted">Apply to positions and connect directly with employers.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
