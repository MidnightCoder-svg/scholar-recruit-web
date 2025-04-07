
<?php
require_once 'config.php';
$page_title = "About Us";
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-4">About ScholarRecruit</h1>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Our Mission</h2>
                    <p>ScholarRecruit is dedicated to bridging the gap between talented students and forward-thinking companies. Our mission is to create meaningful connections that lead to successful careers and organizational growth.</p>
                    
                    <p>We believe that every student deserves access to quality job opportunities that align with their skills and career aspirations. Similarly, companies deserve to find the best talent to help them innovate and succeed.</p>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">What We Offer</h2>
                    
                    <h5 class="mt-4">For Students</h5>
                    <ul>
                        <li>Access to internships and job opportunities from leading companies</li>
                        <li>A platform to showcase your skills and qualifications</li>
                        <li>Simple application process</li>
                        <li>Career resources and guidance</li>
                    </ul>
                    
                    <h5 class="mt-4">For Companies</h5>
                    <ul>
                        <li>Access to a pool of talented and motivated students</li>
                        <li>User-friendly job posting and applicant management</li>
                        <li>Company profile to showcase your organization</li>
                        <li>Tools to efficiently review and manage applications</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Our Story</h2>
                    <p>ScholarRecruit was founded in 2023 with a simple idea: to make the recruitment process better for both students and companies. We noticed that students often struggled to find meaningful opportunities, while companies had difficulty identifying the right talent from traditional recruitment channels.</p>
                    
                    <p>Our platform was built from the ground up with input from both students and employers to ensure it meets the needs of both groups. Today, we're proud to connect thousands of students with companies that value their unique skills and perspectives.</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">Join Our Community</h2>
                    <p>Whether you're a student looking for your next opportunity or a company searching for fresh talent, we invite you to join our growing community. Together, we can build a brighter future through meaningful employment connections.</p>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="register.php" class="btn btn-primary">Register Now</a>
                        <a href="contact.php" class="btn btn-outline-primary">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
