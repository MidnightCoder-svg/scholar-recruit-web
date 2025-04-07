
<?php
require_once 'config.php';
$page_title = "Contact Us";
include 'includes/header.php';

// Handle contact form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // Simple validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Please fill out all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // In a real application, you'd send an email here
        // For this example, we'll just show a success message
        $success_message = 'Thank you for your message! We will get back to you soon.';
        
        // Clear form fields after successful submission
        $name = $email = $subject = $message = '';
    }
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-4">Contact Us</h1>
            
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
            
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title h4 mb-4">Get In Touch</h2>
                            <form action="contact.php" method="post">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Your Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?= htmlspecialchars($message ?? '') ?></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title h4 mb-4">Contact Information</h2>
                            
                            <div class="mb-4">
                                <h5 class="h6">Address</h5>
                                <p class="text-muted mb-0">
                                    123 Tech Avenue<br>
                                    San Francisco, CA 94107<br>
                                    United States
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="h6">Email</h5>
                                <p class="text-muted mb-0">
                                    <a href="mailto:info@scholarrecruit.com" class="text-decoration-none">info@scholarrecruit.com</a>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="h6">Phone</h5>
                                <p class="text-muted mb-0">
                                    <a href="tel:+18001234567" class="text-decoration-none">+1 (800) 123-4567</a>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="h6">Office Hours</h5>
                                <p class="text-muted mb-0">
                                    Monday - Friday: 9:00 AM - 5:00 PM<br>
                                    Saturday - Sunday: Closed
                                </p>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center mt-4">
                                <h5 class="h6 mb-3">Follow Us</h5>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="#" class="text-decoration-none">
                                        <i class="bi bi-facebook fs-4"></i>
                                    </a>
                                    <a href="#" class="text-decoration-none">
                                        <i class="bi bi-twitter fs-4"></i>
                                    </a>
                                    <a href="#" class="text-decoration-none">
                                        <i class="bi bi-linkedin fs-4"></i>
                                    </a>
                                    <a href="#" class="text-decoration-none">
                                        <i class="bi bi-instagram fs-4"></i>
                                    </a>
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
