
<?php
require_once 'config.php';
$page_title = "Page Not Found";
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 fw-bold text-primary">404</h1>
            <p class="display-6 mb-4">Oops! Page not found</p>
            <p class="lead text-muted mb-5">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>
</div>

<script>
    // Log the 404 error
    console.error('404 Error: User attempted to access non-existent page:', window.location.pathname);
</script>

<?php include 'includes/footer.php'; ?>
