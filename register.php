
<?php
require_once 'includes/header.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Define variables and set to empty values
$name = $email = $password = $confirm_password = $role = '';
$name_err = $email_err = $password_err = $confirm_password_err = $role_err = '';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name";
    } else {
        $name = sanitizeInput($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    $email_err = "This email is already taken";
                } else {
                    $email = sanitizeInput($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match";
        }
    }
    
    // Validate role
    if (empty(trim($_POST["role"]))) {
        $role_err = "Please select a role";
    } else {
        $role = sanitizeInput($_POST["role"]);
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($role_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_name, $param_email, $param_password, $param_role);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = $role;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Registration successful
                showMessage("Registration successful! You can now login.", "success");
                redirect("login.php");
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
                <ul class="nav nav-tabs card-header-tabs" id="registerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white <?php echo ($role == 'student' || $role == '') ? 'active' : ''; ?>" 
                           id="student-tab" data-bs-toggle="tab" href="#student" role="tab" 
                           aria-controls="student" aria-selected="true">Student</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-white <?php echo $role == 'company' ? 'active' : ''; ?>" 
                           id="company-tab" data-bs-toggle="tab" href="#company" role="tab" 
                           aria-controls="company" aria-selected="false">Company</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="registerTabsContent">
                    <!-- Student Registration Form -->
                    <div class="tab-pane fade <?php echo ($role == 'student' || $role == '') ? 'show active' : ''; ?>" 
                         id="student" role="tabpanel" aria-labelledby="student-tab">
                        <h3 class="card-title mb-4">Student Registration</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="role" value="student">
                            
                            <div class="mb-3">
                                <label for="student_name" class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                                       id="student_name" value="<?php echo $role == 'student' ? $name : ''; ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo $name_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_email" class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                                       id="student_email" value="<?php echo $role == 'student' ? $email : ''; ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo $email_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                                       id="student_password" required>
                                <div class="invalid-feedback">
                                    <?php echo $password_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" 
                                       class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                                       id="student_confirm_password" required>
                                <div class="invalid-feedback">
                                    <?php echo $confirm_password_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="student_terms" required>
                                <label class="form-check-label" for="student_terms">
                                    I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree before submitting.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Register as Student</button>
                        </form>
                    </div>
                    
                    <!-- Company Registration Form -->
                    <div class="tab-pane fade <?php echo $role == 'company' ? 'show active' : ''; ?>" 
                         id="company" role="tabpanel" aria-labelledby="company-tab">
                        <h3 class="card-title mb-4">Company Registration</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="role" value="company">
                            
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                                       id="company_name" value="<?php echo $role == 'company' ? $name : ''; ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo $name_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company_email" class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                                       id="company_email" value="<?php echo $role == 'company' ? $email : ''; ?>" required>
                                <div class="invalid-feedback">
                                    <?php echo $email_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company_password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                                       id="company_password" required>
                                <div class="invalid-feedback">
                                    <?php echo $password_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company_confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" 
                                       class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                                       id="company_confirm_password" required>
                                <div class="invalid-feedback">
                                    <?php echo $confirm_password_err; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="company_terms" required>
                                <label class="form-check-label" for="company_terms">
                                    I agree to the <a href="#">Terms and Conditions</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree before submitting.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Register as Company</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
