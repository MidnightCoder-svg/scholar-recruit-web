
<?php
require_once 'config.php';
$page_title = "Login";
include 'includes/header.php';

// Process form submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize inputs
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Will be verified with password_verify, so no sanitization
    $role = sanitizeInput($_POST['role']);
    
    if (empty($email) || empty($password) || empty($role)) {
        $error_message = 'Please fill in all fields.';
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE email = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect to appropriate dashboard
                switch ($role) {
                    case ROLE_STUDENT:
                        header("Location: student_dashboard.php");
                        break;
                    case ROLE_COMPANY:
                        header("Location: company_dashboard.php");
                        break;
                    case ROLE_ADMIN:
                        header("Location: admin_dashboard.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit;
            } else {
                $error_message = 'Invalid email or password.';
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <ul class="nav nav-tabs nav-fill mb-4" id="loginTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">Student</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">Company</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">Admin</button>
                </li>
            </ul>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            
            <div class="tab-content" id="loginTabsContent">
                <!-- Student Login -->
                <div class="tab-pane fade show active" id="student" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Student Login</h4>
                            <p class="text-muted small mb-0">Enter your credentials to access your student dashboard</p>
                        </div>
                        <div class="card-body">
                            <form action="login.php" method="post" id="studentLoginForm">
                                <input type="hidden" name="role" value="<?= ROLE_STUDENT ?>">
                                
                                <div class="mb-3">
                                    <label for="student-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="student-email" name="email" placeholder="student@example.com" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="student-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="student-password" name="password" placeholder="******" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <p class="mb-1">Don't have an account? <a href="register.php">Register</a></p>
                            <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
                        </div>
                    </div>
                </div>
                
                <!-- Company Login -->
                <div class="tab-pane fade" id="company" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Company Login</h4>
                            <p class="text-muted small mb-0">Enter your credentials to access your company dashboard</p>
                        </div>
                        <div class="card-body">
                            <form action="login.php" method="post" id="companyLoginForm">
                                <input type="hidden" name="role" value="<?= ROLE_COMPANY ?>">
                                
                                <div class="mb-3">
                                    <label for="company-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="company-email" name="email" placeholder="company@example.com" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="company-password" name="password" placeholder="******" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <p class="mb-1">Don't have a company account? <a href="register.php">Register Company</a></p>
                            <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Login -->
                <div class="tab-pane fade" id="admin" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Admin Login</h4>
                            <p class="text-muted small mb-0">Restricted access for training and placement officers</p>
                        </div>
                        <div class="card-body">
                            <form action="login.php" method="post" id="adminLoginForm">
                                <input type="hidden" name="role" value="<?= ROLE_ADMIN ?>">
                                
                                <div class="mb-3">
                                    <label for="admin-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="admin-email" name="email" placeholder="admin@example.com" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="admin-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="admin-password" name="password" placeholder="******" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle tab switching and form validation
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap's tab functionality is handled automatically
        
        // Handle form validation (simple example)
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
