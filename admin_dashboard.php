
<?php
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !checkRole(ROLE_ADMIN)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Admin Dashboard";
include 'includes/header.php';

// Get statistics
$users_sql = "SELECT 
                SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) as student_count,
                SUM(CASE WHEN role = 'company' THEN 1 ELSE 0 END) as company_count,
                COUNT(*) as total_users
              FROM users";
$users_result = $conn->query($users_sql);
$users_stats = $users_result->fetch_assoc();

$jobs_sql = "SELECT COUNT(*) as total_jobs FROM jobs";
$jobs_result = $conn->query($jobs_sql);
$jobs_stats = $jobs_result->fetch_assoc();

$apps_sql = "SELECT COUNT(*) as total_applications FROM applications";
$apps_result = $conn->query($apps_sql);
$apps_stats = $apps_result->fetch_assoc();

// Get recent users
$recent_users_sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 10";
$recent_users = $conn->query($recent_users_sql);

// Get recent jobs
$recent_jobs_sql = "SELECT j.*, u.name as company_name 
                  FROM jobs j 
                  JOIN users u ON j.company_id = u.id 
                  ORDER BY j.posted_date DESC LIMIT 10";
$recent_jobs = $conn->query($recent_jobs_sql);

// Handle user activation/deactivation (in a real app, you would have an active status field)

?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Admin Panel</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="admin_dashboard.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <a href="admin_jobs.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-briefcase me-2"></i> Manage Jobs
                    </a>
                    <a href="admin_settings.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-gear me-2"></i> Site Settings
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
                            <h2 class="card-title">Welcome, Admin <?= htmlspecialchars($user['name']) ?>!</h2>
                            <p class="card-text">Here's an overview of your platform's activity.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text h2 mb-0"><?= $users_stats['total_users'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Students</h5>
                                <p class="card-text h2 mb-0"><?= $users_stats['student_count'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Companies</h5>
                                <p class="card-text h2 mb-0"><?= $users_stats['company_count'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="display-4 me-3">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Jobs</h5>
                                <p class="card-text h2 mb-0"><?= $jobs_stats['total_jobs'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Users -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Users</h5>
                            <a href="admin_users.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_users->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($user_row = $recent_users->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user_row['name']) ?></td>
                                                    <td><?= htmlspecialchars($user_row['email']) ?></td>
                                                    <td>
                                                        <?php 
                                                        $badge_class = 'bg-secondary';
                                                        switch ($user_row['role']) {
                                                            case 'student':
                                                                $badge_class = 'bg-success';
                                                                break;
                                                            case 'company':
                                                                $badge_class = 'bg-info';
                                                                break;
                                                            case 'admin':
                                                                $badge_class = 'bg-danger';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>">
                                                            <?= ucfirst(htmlspecialchars($user_row['role'])) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($user_row['created_at'])) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No users found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Jobs -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Jobs</h5>
                            <a href="admin_jobs.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_jobs->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Company</th>
                                                <th>Type</th>
                                                <th>Posted</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($job['title']) ?>
                                                        </a>
                                                    </td>
                                                    <td><?= htmlspecialchars($job['company_name']) ?></td>
                                                    <td><span class="badge bg-primary"><?= htmlspecialchars($job['type']) ?></span></td>
                                                    <td><?= date('M d, Y', strtotime($job['posted_date'])) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No jobs posted yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Overview -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Platform Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="h6">User Distribution</h5>
                                <div class="progress" style="height: 25px;">
                                    <?php 
                                    $student_percentage = ($users_stats['student_count'] / $users_stats['total_users']) * 100;
                                    $company_percentage = ($users_stats['company_count'] / $users_stats['total_users']) * 100;
                                    $admin_percentage = 100 - $student_percentage - $company_percentage;
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $student_percentage ?>%" 
                                         aria-valuenow="<?= $student_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($student_percentage) ?>% Students
                                    </div>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $company_percentage ?>%" 
                                         aria-valuenow="<?= $company_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($company_percentage) ?>% Companies
                                    </div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $admin_percentage ?>%" 
                                         aria-valuenow="<?= $admin_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($admin_percentage) ?>% Admins
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="h6">Job Statistics</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Jobs
                                        <span class="badge bg-primary rounded-pill"><?= $jobs_stats['total_jobs'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Applications
                                        <span class="badge bg-primary rounded-pill"><?= $apps_stats['total_applications'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Average Applications per Job
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $jobs_stats['total_jobs'] > 0 ? round($apps_stats['total_applications'] / $jobs_stats['total_jobs'], 1) : 0 ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="h6">Quick Actions</h5>
                            <div class="list-group">
                                <a href="admin_users.php?action=new" class="list-group-item list-group-item-action">
                                    <i class="bi bi-person-plus me-2"></i> Add New User
                                </a>
                                <a href="admin_jobs.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-briefcase me-2"></i> Manage Job Listings
                                </a>
                                <a href="admin_reports.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-file-earmark-text me-2"></i> Generate Reports
                                </a>
                                <a href="admin_settings.php" class="list-group-item list-group-item-action">
                                    <i class="bi bi-gear me-2"></i> System Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
