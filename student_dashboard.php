
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

// Get student's applications
$applications_sql = "SELECT a.id, a.status, a.applied_date, j.title, j.location, j.type, u.name as company_name 
                    FROM applications a 
                    JOIN jobs j ON a.job_id = j.id 
                    JOIN users u ON j.company_id = u.id 
                    WHERE a.student_id = ? 
                    ORDER BY a.applied_date DESC";
$applications_stmt = $conn->prepare($applications_sql);
$applications_stmt->bind_param("i", $user['id']);
$applications_stmt->execute();
$applications_result = $applications_stmt->get_result();
$applications_count = $applications_result->num_rows;

// Count interviews (applications with status "Interview Scheduled")
$interviews_sql = "SELECT COUNT(*) as interview_count 
                  FROM applications 
                  WHERE student_id = ? AND status = 'Interview Scheduled'";
$interviews_stmt = $conn->prepare($interviews_sql);
$interviews_stmt->bind_param("i", $user['id']);
$interviews_stmt->execute();
$interviews_result = $interviews_stmt->get_result();
$interviews_count = $interviews_result->fetch_assoc()['interview_count'];

// Get recent notifications (simplified for this example)
$notifications = [
    [
        'id' => 1,
        'message' => 'Interview scheduled for Software Engineering Intern at TechSolutions Inc.',
        'date' => 'March 25, 2025',
        'is_read' => false
    ],
    [
        'id' => 2,
        'message' => 'Your application for Data Analyst Intern has been reviewed.',
        'date' => 'March 20, 2025',
        'is_read' => true
    ],
    [
        'id' => 3,
        'message' => 'New internship opportunities matching your profile are available!',
        'date' => 'March 18, 2025',
        'is_read' => true
    ],
];
$unread_notifications = count(array_filter($notifications, function($n) { return !$n['is_read']; }));
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($user['photo_url'])): ?>
                        <img src="<?= htmlspecialchars($user['photo_url']) ?>" alt="Profile Photo" class="rounded-circle img-fluid mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <a href="student_profile.php" class="btn btn-outline-primary btn-sm w-100 mb-2">Edit Profile</a>
                </div>
            </div>
            
            <div class="list-group mb-4">
                <a href="student_dashboard.php" class="list-group-item list-group-item-action active">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="student_profile.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-person me-2"></i> My Profile
                </a>
                <a href="jobs.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-briefcase me-2"></i> Browse Jobs
                </a>
                <a href="student_applications.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-file-earmark-text me-2"></i> My Applications
                </a>
                <a href="notifications.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-bell me-2"></i> Notifications
                    <?php if ($unread_notifications > 0): ?>
                        <span class="badge bg-danger rounded-pill float-end"><?= $unread_notifications ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Dashboard</h3>
                <a href="jobs.php" class="btn btn-primary">Browse Jobs</a>
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
                                <h6 class="text-muted mb-1">Applications</h6>
                                <h4 class="mb-0"><?= $applications_count ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="bi bi-calendar-check text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Interviews</h6>
                                <h4 class="mb-0"><?= $interviews_count ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="bi bi-bell text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Notifications</h6>
                                <h4 class="mb-0"><?= $unread_notifications ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">Applications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">Notifications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab">Resources</button>
                </li>
            </ul>
            
            <div class="tab-content" id="dashboardTabsContent">
                <!-- Applications Tab -->
                <div class="tab-pane fade show active" id="applications" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Your Applications</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($applications_count > 0): ?>
                                <div class="list-group">
                                    <?php while ($application = $applications_result->fetch_assoc()): ?>
                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1"><?= htmlspecialchars($application['title']) ?></h5>
                                                <small>
                                                    Applied: <?= date('M j, Y', strtotime($application['applied_date'])) ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?= htmlspecialchars($application['company_name']) ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($application['location']) ?>
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-briefcase me-1"></i> <?= htmlspecialchars($application['type']) ?>
                                                </small>
                                                <span class="badge <?= 
                                                    $application['status'] === 'pending' ? 'bg-secondary' : 
                                                    ($application['status'] === 'reviewed' ? 'bg-info' : 
                                                    ($application['status'] === 'Interview Scheduled' ? 'bg-primary' : 
                                                    ($application['status'] === 'rejected' ? 'bg-danger' : 'bg-success'))) 
                                                ?>">
                                                    <?= htmlspecialchars(ucfirst($application['status'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-briefcase text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5>No applications yet</h5>
                                    <p class="text-muted">You haven't applied to any jobs or internships yet.</p>
                                    <a href="jobs.php" class="btn btn-primary mt-2">Browse Jobs</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Notifications</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($notifications) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($notifications as $notification): ?>
                                        <div class="list-group-item list-group-item-action <?= !$notification['is_read'] ? 'list-group-item-primary' : '' ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= htmlspecialchars($notification['message']) ?></h6>
                                                <small><?= htmlspecialchars($notification['date']) ?></small>
                                            </div>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-primary">New</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-bell text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5>No notifications</h5>
                                    <p class="text-muted">You don't have any notifications at the moment.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Resources Tab -->
                <div class="tab-pane fade" id="resources" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Career Resources</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                                </div>
                                                <h5 class="mb-0">Resume Building Guide</h5>
                                            </div>
                                            <p class="card-text">Learn how to create an effective resume that stands out to recruiters.</p>
                                            <a href="#" class="btn btn-link p-0">View Resource</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="bi bi-people text-primary"></i>
                                                </div>
                                                <h5 class="mb-0">Interview Preparation</h5>
                                            </div>
                                            <p class="card-text">Tips and techniques to help you excel in your interviews.</p>
                                            <a href="#" class="btn btn-link p-0">View Resource</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="bi bi-journal-code text-primary"></i>
                                                </div>
                                                <h5 class="mb-0">Skill Development</h5>
                                            </div>
                                            <p class="card-text">Resources to help you develop in-demand skills for your field.</p>
                                            <a href="#" class="btn btn-link p-0">View Resource</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="bi bi-briefcase text-primary"></i>
                                                </div>
                                                <h5 class="mb-0">Industry Insights</h5>
                                            </div>
                                            <p class="card-text">Stay up-to-date with trends and insights in your industry.</p>
                                            <a href="#" class="btn btn-link p-0">View Resource</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
