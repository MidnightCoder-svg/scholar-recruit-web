
<?php
require_once 'config.php';

// Check if user is logged in and is a student
if (!isLoggedIn() || !checkRole(ROLE_STUDENT)) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$page_title = "Student Profile";
include 'includes/header.php';

// Handle profile update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $bio = sanitizeInput($_POST['bio']);
    $education = sanitizeInput($_POST['education']);
    $experience = sanitizeInput($_POST['experience']);
    $phone = sanitizeInput($_POST['phone']);
    $skills = sanitizeInput($_POST['skills']);
    
    // Validate required fields
    if (empty($name) || empty($email)) {
        $error_message = 'Name and email are required fields.';
    } else {
        // Check if email already exists for another user
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $user['id']);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = 'Email address is already in use by another account.';
        } else {
            // Update profile information
            $update_sql = "UPDATE users SET name = ?, email = ?, bio = ?, education = ?, 
                           experience = ?, phone = ?, skills = ? 
                           WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssssssi", $name, $email, $bio, $education, 
                                   $experience, $phone, $skills, $user['id']);
            
            if ($update_stmt->execute()) {
                $success_message = 'Profile updated successfully!';
                // Refresh user data
                $user = getCurrentUser();
            } else {
                $error_message = 'Error updating profile: ' . $conn->error;
            }
        }
    }
    
    // Handle password update if requested
    if (!empty($_POST['new_password']) && !empty($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $error_message = 'Current password is incorrect.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'New passwords do not match.';
        } elseif (strlen($new_password) < 8) {
            $error_message = 'Password must be at least 8 characters long.';
        } else {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_pass_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_pass_stmt = $conn->prepare($update_pass_sql);
            $update_pass_stmt->bind_param("si", $hashed_password, $user['id']);
            
            if ($update_pass_stmt->execute()) {
                $success_message = 'Password updated successfully!';
            } else {
                $error_message = 'Error updating password: ' . $conn->error;
            }
        }
    }
    
    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
            $error_message = 'Only JPG, PNG, and GIF images are allowed.';
        } elseif ($_FILES['profile_photo']['size'] > $max_size) {
            $error_message = 'File size must be less than 2MB.';
        } else {
            $upload_dir = 'uploads/profile_photos/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'student_' . $user['id'] . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                // Update user's photo URL in the database
                $photo_url = $target_file;
                $update_photo_sql = "UPDATE users SET photo_url = ? WHERE id = ?";
                $update_photo_stmt = $conn->prepare($update_photo_sql);
                $update_photo_stmt->bind_param("si", $photo_url, $user['id']);
                
                if ($update_photo_stmt->execute()) {
                    $success_message = 'Profile photo updated successfully!';
                    // Refresh user data
                    $user = getCurrentUser();
                } else {
                    $error_message = 'Error updating profile photo in database.';
                }
            } else {
                $error_message = 'Error uploading profile photo.';
            }
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
                    <a href="student_dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="student_profile.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                    <a href="jobs.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-briefcase me-2"></i> Browse Jobs
                    </a>
                    <a href="student_applications.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-earmark-text me-2"></i> My Applications
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
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    <form action="student_profile.php" method="post" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <?php if (!empty($user['photo_url'])): ?>
                                        <img src="<?= htmlspecialchars($user['photo_url']) ?>" alt="Profile Photo" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 4rem;">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="profile_photo" class="form-label">Profile Photo</label>
                                        <input class="form-control form-control-sm" id="profile_photo" name="profile_photo" type="file">
                                        <div class="form-text">Max size: 2MB. JPG, PNG, GIF only.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us about yourself"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="education" class="form-label">Education</label>
                            <textarea class="form-control" id="education" name="education" rows="3" placeholder="Your educational background"><?= htmlspecialchars($user['education'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="experience" class="form-label">Work Experience</label>
                            <textarea class="form-control" id="experience" name="experience" rows="3" placeholder="Your work experience"><?= htmlspecialchars($user['experience'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills (comma-separated)</label>
                            <input type="text" class="form-control" id="skills" name="skills" value="<?= htmlspecialchars($user['skills'] ?? '') ?>" placeholder="e.g. JavaScript, React, Node.js">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="student_profile.php" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                            <div class="form-text">Password must be at least 8 characters long</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
