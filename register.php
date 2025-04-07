
<?php
require_once 'config.php';
$page_title = "Register";
include 'includes/header.php';

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitizeInput($_POST['role']);
    
    // Additional fields based on role
    $college = isset($_POST['college']) ? sanitizeInput($_POST['college']) : '';
    $degree = isset($_POST['degree']) ? sanitizeInput($_POST['degree']) : '';
    $graduation_year = isset($_POST['graduation_year']) ? sanitizeInput($_POST['graduation_year']) : '';
    $website = isset($_POST['website']) ? sanitizeInput($_POST['website']) : '';
    $industry = isset($_POST['industry']) ? sanitizeInput($_POST['industry']) : '';
    $location = isset($_POST['location']) ? sanitizeInput($_POST['location']) : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = 'Email address is already registered.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $insert_sql = "INSERT INTO users (name, email, password, role";
            $insert_params = "ssss";
            $insert_values = [$name, $email, $hashed_password, $role];
            
            // Add role-specific fields
            if ($role === ROLE_STUDENT) {
                if (empty($college) || empty($degree) || empty($graduation_year)) {
                    $error_message = 'Please fill in all student details.';
                    goto end_registration;  // Skip registration
                }
                $insert_sql .= ", education";
                $insert_params .= "s";
                $education = "College: $college\nDegree: $degree\nGraduation Year: $graduation_year";
                $insert_values[] = $education;
            } elseif ($role === ROLE_COMPANY) {
                if (empty($industry) || empty($location)) {
                    $error_message = 'Please fill in all company details.';
                    goto end_registration;  // Skip registration
                }
                $insert_sql .= ", website, description, location";
                $insert_params .= "sss";
                $insert_values[] = $website;
                $insert_values[] = $industry; // Using description field for industry
                $insert_values[] = $location;
            }
            
            $insert_sql .= ") VALUES (" . str_repeat("?,", count($insert_values) - 1) . "?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param($insert_params, ...$insert_values);
            
            if ($insert_stmt->execute()) {
                $success_message = 'Registration successful! You can now log in.';
            } else {
                $error_message = 'Error creating account: ' . $conn->error;
            }
        }
    }
}

end_registration:  // Label for skipping registration on validation failure
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success_message ?>
                    <a href="login.php" class="alert-link">Click here to login</a>
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
                <div class="card-header">
                    <h3 class="mb-0">Create an Account</h3>
                    <p class="text-muted mb-0">Join ScholarRecruit to find the perfect opportunity</p>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-fill mb-4" id="registerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">Student</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">Company</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="registerTabsContent">
                        <!-- Student Registration Form -->
                        <div class="tab-pane fade show active" id="student" role="tabpanel">
                            <form action="register.php" method="post" id="studentForm" class="needs-validation" novalidate>
                                <input type="hidden" name="role" value="<?= ROLE_STUDENT ?>">
                                
                                <div class="mb-3">
                                    <label for="student-name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="student-name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="student-email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="student-email" name="email" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="student-password" class="form-label">Password *</label>
                                        <input type="password" class="form-control" id="student-password" name="password" minlength="6" required>
                                        <div class="form-text">Must be at least 6 characters long</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="student-confirm-password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="student-confirm-password" name="confirm_password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="student-college" class="form-label">College/University *</label>
                                    <input type="text" class="form-control" id="student-college" name="college" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="student-degree" class="form-label">Degree/Program *</label>
                                        <input type="text" class="form-control" id="student-degree" name="degree" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="student-graduation-year" class="form-label">Graduation Year *</label>
                                        <input type="text" class="form-control" id="student-graduation-year" name="graduation_year" pattern="[0-9]{4}" required>
                                        <div class="form-text">4-digit year (e.g., 2025)</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="student-terms" required>
                                    <label class="form-check-label" for="student-terms">I agree to the <a href="#" target="_blank">Terms and Conditions</a></label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Create Account</button>
                            </form>
                        </div>
                        
                        <!-- Company Registration Form -->
                        <div class="tab-pane fade" id="company" role="tabpanel">
                            <form action="register.php" method="post" id="companyForm" class="needs-validation" novalidate>
                                <input type="hidden" name="role" value="<?= ROLE_COMPANY ?>">
                                
                                <div class="mb-3">
                                    <label for="company-name" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="company-name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company-email" class="form-label">Company Email *</label>
                                    <input type="email" class="form-control" id="company-email" name="email" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="company-password" class="form-label">Password *</label>
                                        <input type="password" class="form-control" id="company-password" name="password" minlength="6" required>
                                        <div class="form-text">Must be at least 6 characters long</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="company-confirm-password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="company-confirm-password" name="confirm_password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company-website" class="form-label">Company Website</label>
                                    <input type="url" class="form-control" id="company-website" name="website" placeholder="https://example.com">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company-industry" class="form-label">Industry *</label>
                                    <input type="text" class="form-control" id="company-industry" name="industry" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company-location" class="form-label">Location *</label>
                                    <input type="text" class="form-control" id="company-location" name="location" placeholder="City, State, Country" required>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="company-terms" required>
                                    <label class="form-check-label" for="company-terms">I agree to the <a href="#" target="_blank">Terms and Conditions</a></label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Create Account</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
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
        
        // Handle form validation
        const forms = document.querySelectorAll('.needs-validation');
        
        // Password confirmation validation
        const validatePassword = function(form) {
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        };
        
        Array.from(forms).forEach(form => {
            // Password change event listener
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword) {
                password.addEventListener('change', () => validatePassword(form));
                confirmPassword.addEventListener('keyup', () => validatePassword(form));
            }
            
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
