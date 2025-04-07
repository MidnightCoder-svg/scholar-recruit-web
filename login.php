
<?php
require_once 'includes/header.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Define variables and set to empty values
$email = $password = $role = '';
$email_err = $password_err = $role_err = '';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email";
    } else {
        $email = sanitizeInput($_POST["email"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select a role";
    } else {
        $role = sanitizeInput($_POST["role"]);
    }
    
    // Check input errors before processing
    if (empty($email_err) && empty($password_err) && empty($role_err)) {
        // Prepare a select statement
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_email, $param_role);
            
            // Set parameters
            $param_email = $email;
            $param_role = $role;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                
                // Check if email exists
                if ($stmt->num_rows == 1) {                    
                    // Bind result variables
                    $stmt->bind_result($id, $name, $email, $hashed_password, $user_role);
                    
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["user_id"] = $id;
                            $_SESSION["name"] = $name;
                            $_SESSION["email"] = $email;
                            $_SESSION["role"] = $user_role;
                            
                            // Redirect user to dashboard based on role
                            if ($user_role == 'student') {
                                redirect("student_dashboard.php");
                            } elseif ($user_role == 'company') {
                                redirect("company_dashboard.php");
                            } elseif ($user_role == 'admin') {
                                redirect("admin_dashboard.php");
                            } else {
                                redirect("index.php");
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered is not valid";
                        }
                    }
                } else {
                    // Display an error message if email doesn't exist or role doesn't match
                    $email_err = "No account found with that email and role";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <ul class="nav nav-tabs card-header-tabs" id="loginTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white <?php echo ($role == 'student' || $role == '') ? 'active' : ''; ?>" 
                           id="student-tab" data-bs-toggle="tab" href="#student-login" role="tab" 
                           aria-controls="student-login" aria-selected="true">Student</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white <?php echo $role == 'company' ? 'active' : ''; ?>" 
                           id="company-tab" data-bs-toggle="tab" href="#company-login" role="tab" 
                           aria-controls="company-login" aria-selected="false">Company</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white <?php echo $role == 'admin' ? 'active' : ''; ?>" 
                           id="admin-tab" data-bs-toggle="tab" href="#admin-login" role="tab" 
                           aria-controls="admin-login" aria-selected="false">Admin</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="loginTabsContent">
                    <!-- Student Login Form -->
                    <div class="tab-pane fade <?php echo ($role == 'student' || $role == '') ? 'show active' : ''; ?>" 
                         id="student-login" role="tabpanel" aria-labelledby="student-tab">
                        <h3 class="card-title mb-4">Student Login</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="role" value="student">
                            
                            <div class="mb-3">
                                <label for="student_email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err) && $role == 'student') ? 'is-invalid' : ''; ?>" 
                                       id="student_email" value="<?php echo $role == 'student' ? $email : ''; ?>" required>
                                <?php if (!empty($email_err) && $role == 'student'): ?>
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err) && $role == 'student') ? 'is-invalid' : ''; ?>" 
                                       id="student_password" required>
                                <?php if (!empty($password_err) && $role == 'student'): ?>
                                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="student_remember">
                                <label class="form-check-label" for="student_remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    
                    <!-- Company Login Form -->
                    <div class="tab-pane fade <?php echo $role == 'company' ? 'show active' : ''; ?>" 
                         id="company-login" role="tabpanel" aria-labelledby="company-tab">
                        <h3 class="card-title mb-4">Company Login</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="role" value="company">
                            
                            <div class="mb-3">
                                <label for="company_email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err) && $role == 'company') ? 'is-invalid' : ''; ?>" 
                                       id="company_email" value="<?php echo $role == 'company' ? $email : ''; ?>" required>
                                <?php if (!empty($email_err) && $role == 'company'): ?>
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company_password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err) && $role == 'company') ? 'is-invalid' : ''; ?>" 
                                       id="company_password" required>
                                <?php if (!empty($password_err) && $role == 'company'): ?>
                                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="company_remember">
                                <label class="form-check-label" for="company_remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    
                    <!-- Admin Login Form -->
                    <div class="tab-pane fade <?php echo $role == 'admin' ? 'show active' : ''; ?>" 
                         id="admin-login" role="tabpanel" aria-labelledby="admin-tab">
                        <h3 class="card-title mb-4">Admin Login</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="role" value="admin">
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err) && $role == 'admin') ? 'is-invalid' : ''; ?>" 
                                       id="admin_email" value="<?php echo $role == 'admin' ? $email : ''; ?>" required>
                                <?php if (!empty($email_err) && $role == 'admin'): ?>
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err) && $role == 'admin') ? 'is-invalid' : ''; ?>" 
                                       id="admin_password" required>
                                <?php if (!empty($password_err) && $role == 'admin'): ?>
                                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="admin_remember">
                                <label class="form-check-label" for="admin_remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p><a href="forgot_password.php">Forgot password?</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
